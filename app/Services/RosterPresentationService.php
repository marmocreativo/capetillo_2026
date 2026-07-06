<?php

namespace App\Services;

use App\Models\Roster;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing\File as DrawingFile;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\IOFactory;

class RosterPresentationService
{
    protected const GOLD = 'DCA54A';
    protected const DARK_BG = '1A1A1A';
    protected const DARK_CARD = '262626';
    protected const WHITE = 'FFFFFF';
    protected const BLACK = '000000';
    protected const FONT = 'Calibri';
    protected const OVERLAY_ALPHA_HEX = 'CC000000';

    protected const SLIDE_W = 540;
    protected const SLIDE_H = 720;

    protected array $backgroundImagePaths = [];
    protected array $tempFiles = [];

    public function generate(Roster $roster): string
    {
        $presentation = new PhpPresentation();
        $presentation->getLayout()
            ->setCX(self::SLIDE_W, \PhpOffice\PhpPresentation\DocumentLayout::UNIT_PIXEL)
            ->setCY(self::SLIDE_H, \PhpOffice\PhpPresentation\DocumentLayout::UNIT_PIXEL);

        $this->addCoverSlide($presentation, $roster);

        $entries = $roster->rosterTalents()->with(['talent.categories', 'roster'])->get();

        if ($roster->separar_por_categoria) {
            $grouped = $entries->groupBy(function ($entry) {
                return optional($entry->talent?->categories->sortBy('name')->first())->name ?? 'Sin categoría';
            });

            foreach ($grouped as $categoryName => $group) {
                $this->addSectionSlide($presentation, $categoryName, $roster);
                $this->addTalentSlides($presentation, $group, $roster->talentos_por_pagina);
            }
        } else {
            $this->addTalentSlides($presentation, $entries, $roster->talentos_por_pagina);
        }

        $this->addClosingSlide($presentation, $roster);

        $filename = 'roster-' . $roster->id . '-' . now()->timestamp . '.pptx';
        $outputPath = storage_path('app/temp/' . $filename);

        if (! is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = IOFactory::createWriter($presentation, 'PowerPoint2007');
        $writer->save($outputPath);

        $this->cleanupTempFiles();

        return $outputPath;
    }

    // =========================================================================
    // PORTADA
    // =========================================================================

    protected function addCoverSlide(PhpPresentation $presentation, Roster $roster): void
    {
        $slide = $presentation->getActiveSlide() ?: $presentation->createSlide();
        $this->addBackgroundImage($slide, 'presentaciones');

        // Alturas estimadas de cada fila para poder centrar el bloque completo verticalmente.
        $logosHeight = 80;
        $separatorHeight = 5;
        $titleHeight = 60;
        $introHeight = 90;
        $rowGap = 22;

        $totalBlockHeight = $logosHeight + $separatorHeight + $titleHeight + $introHeight + ($rowGap * 3);
        $y = (self::SLIDE_H - $totalBlockHeight) / 2;

        // Fila 1: logos equidistantes, máximo 80% del ancho.
        $y = $this->drawLogosRow($slide, $roster, $y, 0.8, $logosHeight, 25) + $rowGap;

        // Fila 2: separador.
        $y = $this->addCenteredImage($slide, 'images/separador.png', $y, $separatorHeight) + $rowGap;

        // Fila 3: nombre del roster, mayúsculas, negrita grande ("black").
        $title = $slide->createRichTextShape()
            ->setHeight($titleHeight)
            ->setWidth(self::SLIDE_W - 60)
            ->setOffsetX(30)
            ->setOffsetY($y);
        $title->setWrap(RichText::WRAP_SQUARE);
        $run = $title->createTextRun(mb_strtoupper($roster->name));
        $run->getFont()->setBold(true)->setSize(38)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);
        $title->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $y += $titleHeight + $rowGap;

        // Fila 4: texto de presentación, chico, peso regular.
        if ($roster->intro_text) {
            $intro = $slide->createRichTextShape()
                ->setHeight($introHeight)
                ->setWidth(self::SLIDE_W - 140)
                ->setOffsetX(70)
                ->setOffsetY($y);
            $intro->setWrap(RichText::WRAP_SQUARE);
            $introRun = $intro->createTextRun($roster->intro_text);
            $introRun->getFont()->setBold(false)->setSize(13)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);
            $intro->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    // =========================================================================
    // SEPARADOR DE CATEGORÍA
    // =========================================================================

    protected function addSectionSlide(PhpPresentation $presentation, string $categoryName, ?Roster $roster = null): void
    {
        $slide = $presentation->createSlide();
        $this->addBackgroundImage($slide, 'presentaciones');

        $logosHeight = 50;
        $separatorHeight = 5;
        $titleHeight = 60;
        $rowGap = 25;

        $totalBlockHeight = $logosHeight + $separatorHeight + $titleHeight + ($rowGap * 2);
        $y = (self::SLIDE_H - $totalBlockHeight) / 2;

        $y = $this->drawLogosRow($slide, $roster, $y, 0.8, $logosHeight, 25) + $rowGap;

        $y = $this->addCenteredImage($slide, 'images/separador.png', $y, $separatorHeight) + $rowGap;

        $title = $slide->createRichTextShape()
            ->setHeight($titleHeight)
            ->setWidth(self::SLIDE_W - 100)
            ->setOffsetX(50)
            ->setOffsetY($y);
        $run = $title->createTextRun(mb_strtoupper($categoryName));
        $run->getFont()->setBold(true)->setSize(32)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);
        $title->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    // =========================================================================
    // DISTRIBUCIÓN DE TALENTOS POR PÁGINA
    // =========================================================================

    protected function addTalentSlides(PhpPresentation $presentation, $entries, int $perPage): void
    {
        if ($perPage === 0) {
            $this->addListSlides($presentation, $entries);
            return;
        }

        if ($perPage === 1) {
            foreach ($entries as $entry) {
                $slide = $presentation->createSlide();
                $this->addTalentFullBleedSlide($slide, $entry);
            }
            return;
        }

        if ($perPage === 2) {
            foreach ($entries->chunk(2) as $chunk) {
                $slide = $presentation->createSlide();
                $this->addBackgroundImage($slide, 'contenido');
                $this->addTwoPerPageRows($slide, $chunk);
            }
            return;
        }

        // perPage === 4
        foreach ($entries->chunk(4) as $chunk) {
            $slide = $presentation->createSlide();
            $this->addBackgroundImage($slide, 'contenido');
            $this->addFourPerPageRows($slide, $chunk);
        }
    }

    // =========================================================================
    // 1 TALENTO POR PÁGINA (pantalla completa + pleca)
    // =========================================================================

    protected function addTalentFullBleedSlide(Slide $slide, $entry): void
    {
        $imagePath = $this->resolveJpegPath($entry->talent?->cover_image, self::SLIDE_W, self::SLIDE_H);

        if ($imagePath) {
            $drawing = new DrawingFile();
            $drawing->setPath($imagePath)
                ->setResizeProportional(false)
                ->setWidth(self::SLIDE_W)
                ->setHeight(self::SLIDE_H)
                ->setOffsetX(0)
                ->setOffsetY(0);
            $slide->addShape($drawing);
        } else {
            $this->addBackgroundImage($slide, 'contenido');
        }

        // Pleca de texto (imagen) en vez de franja negra semitransparente.
        $overlayHeight = 200;
        $overlayY = self::SLIDE_H - $overlayHeight;
        $pleca = public_path('images/pleca_texto.jpg');

        if (file_exists($pleca)) {
            $drawing = new DrawingFile();
            $drawing->setPath($pleca)
                ->setResizeProportional(false)
                ->setWidth(self::SLIDE_W)
                ->setHeight($overlayHeight)
                ->setOffsetX(0)
                ->setOffsetY($overlayY);
            $slide->addShape($drawing);
        }

        $y = $overlayY + 20;

        $nameShape = $slide->createRichTextShape()
            ->setHeight(28)
            ->setWidth(self::SLIDE_W - 50)
            ->setOffsetX(25)
            ->setOffsetY($y);
        $nameRun = $nameShape->createTextRun($entry->nombre ?? $entry->talent?->name ?? '');
        $nameRun->getFont()->setBold(true)->setSize(18)->setColor(new Color('FF' . self::BLACK))->setName(self::FONT);
        $nameShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $y += 30;

        $summaryShape = $slide->createRichTextShape()
            ->setHeight(48)
            ->setWidth(self::SLIDE_W - 50)
            ->setOffsetX(25)
            ->setOffsetY($y);
        $summaryShape->setWrap(RichText::WRAP_SQUARE);
        $summaryRun = $summaryShape->createTextRun($entry->resumen_corto ?? '');
        $summaryRun->getFont()->setSize(8)->setColor(new Color('FF' . self::BLACK))->setName(self::FONT);
        $summaryShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $y += 50;

        $this->addHonorariosLine($slide, $entry, 25, $y, self::SLIDE_W - 50, self::BLACK, Alignment::HORIZONTAL_CENTER);

        $y += 26;

        // Pleca negra detrás de la fila de logos.
        $logosStripHeight = 70;
        $logosStrip = $slide->createRichTextShape()
            ->setHeight($logosStripHeight)
            ->setWidth(self::SLIDE_W)
            ->setOffsetX(0)
            ->setOffsetY($y);
        $logosStrip->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF' . self::BLACK));

        $logosY = $y + (($logosStripHeight - 45) / 2);

        // Fila de logos, máximo 30% del ancho.
        $this->drawLogosRow($slide, $entry->roster, $logosY, 0.3, 45, 15);
    }

    protected function addHonorariosLine(Slide $slide, $entry, float $x, float $y, float $width, string $colorHex = null, string $align = Alignment::HORIZONTAL_LEFT): void
    {
        $roster = $entry->roster ?? null;

        if (! $roster || ! $roster->mostrar_honorarios) {
            return;
        }

        $monto = number_format((float) ($entry->honorarios ?? 0), 2);
        $colorHex = $colorHex ?? self::GOLD;

        $shape = $slide->createRichTextShape()
            ->setHeight(24)
            ->setWidth($width)
            ->setOffsetX($x)
            ->setOffsetY($y);
        $run = $shape->createTextRun("Honorarios: \${$monto} MXN");
        $run->getFont()->setBold(true)->setSize(11)->setColor(new Color('FF' . $colorHex))->setName(self::FONT);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal($align);
    }

    // =========================================================================
    // 2 TALENTOS POR PÁGINA (filas 1/2 x 1/2)
    // =========================================================================

    protected function addTwoPerPageRows(Slide $slide, $chunk): void
    {
        $margin = 30;
        $gap = 20;
        $rowHeight = 320;
        $imageWidth = (self::SLIDE_W - ($margin * 2) - 15) / 2;
        $textX = $margin + $imageWidth + 15;
        $textWidth = self::SLIDE_W - $textX - $margin;

        $y = 40;

        foreach ($chunk->values() as $entry) {
            $imagePath = $this->resolveJpegPath($entry->talent?->cover_image, (int) $imageWidth, $rowHeight);

            if ($imagePath) {
                $drawing = new DrawingFile();
                $drawing->setPath($imagePath)
                    ->setResizeProportional(false)
                    ->setWidth($imageWidth)
                    ->setHeight($rowHeight)
                    ->setOffsetX($margin)
                    ->setOffsetY($y);
                $slide->addShape($drawing);
            }

            $nameShape = $slide->createRichTextShape()
                ->setHeight(40)
                ->setWidth($textWidth)
                ->setOffsetX($textX)
                ->setOffsetY($y);
            $nameRun = $nameShape->createTextRun($entry->nombre ?? $entry->talent?->name ?? '');
            $nameRun->getFont()->setBold(true)->setSize(18)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);

            $summaryShape = $slide->createRichTextShape()
                ->setHeight($rowHeight - 85)
                ->setWidth($textWidth)
                ->setOffsetX($textX)
                ->setOffsetY($y + 58);
            $summaryShape->setWrap(RichText::WRAP_SQUARE);
            $summaryRun = $summaryShape->createTextRun($entry->resumen_corto ?? '');
            $summaryRun->getFont()->setSize(10)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);

            $this->addHonorariosLine($slide, $entry, $textX, $y + $rowHeight - 24, $textWidth);

            $y += $rowHeight + $gap;
        }
    }

    // =========================================================================
    // 4 TALENTOS POR PÁGINA (filas 1/3 x 2/3, texto chico)
    // =========================================================================

    protected function addFourPerPageRows(Slide $slide, $chunk): void
    {
        $margin = 25;
        $gap = 12;
        $rowHeight = 155;
        $imageWidth = (self::SLIDE_W - ($margin * 2) - 10) / 3;
        $textX = $margin + $imageWidth + 10;
        $textWidth = self::SLIDE_W - $textX - $margin;

        $y = 30;

        foreach ($chunk->values() as $entry) {
            $imagePath = $this->resolveJpegPath($entry->talent?->cover_image, (int) $imageWidth, $rowHeight);

            if ($imagePath) {
                $drawing = new DrawingFile();
                $drawing->setPath($imagePath)
                    ->setResizeProportional(false)
                    ->setWidth($imageWidth)
                    ->setHeight($rowHeight)
                    ->setOffsetX($margin)
                    ->setOffsetY($y);
                $slide->addShape($drawing);
            }

            $nameShape = $slide->createRichTextShape()
                ->setHeight(24)
                ->setWidth($textWidth)
                ->setOffsetX($textX)
                ->setOffsetY($y);
            $nameRun = $nameShape->createTextRun($entry->nombre ?? $entry->talent?->name ?? '');
            $nameRun->getFont()->setBold(true)->setSize(12)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);

            $summaryShape = $slide->createRichTextShape()
                ->setHeight($rowHeight - 46)
                ->setWidth($textWidth)
                ->setOffsetX($textX)
                ->setOffsetY($y + 26);
            $summaryShape->setWrap(RichText::WRAP_SQUARE);
            $summaryRun = $summaryShape->createTextRun($entry->resumen_corto ?? '');
            $summaryRun->getFont()->setSize(8)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);

            $this->addHonorariosLine($slide, $entry, $textX, $y + $rowHeight - 18, $textWidth);

            $y += $rowHeight + $gap;
        }
    }

    // =========================================================================
    // LISTA (tabla con bordes)
    // =========================================================================

    protected function addListSlides(PhpPresentation $presentation, $entries): void
    {
        $margin = 25;
        $headerHeight = 26;
        $rowHeight = 46;
        $tableWidth = self::SLIDE_W - ($margin * 2);

        $showHonorarios = (bool) (optional($entries->first())->roster?->mostrar_honorarios ?? false);
        $cols = $showHonorarios ? 3 : 2;

        $colWidths = $showHonorarios
            ? [(int) ($tableWidth * 0.28), (int) ($tableWidth * 0.50), (int) ($tableWidth * 0.22)]
            : [(int) ($tableWidth * 0.32), (int) ($tableWidth * 0.68)];

        $maxRowsPerSlide = intdiv(self::SLIDE_H - ($margin * 2) - $headerHeight, $rowHeight);

        foreach ($entries->chunk($maxRowsPerSlide) as $chunk) {
            $slide = $presentation->createSlide();
            $this->addBackgroundImage($slide, 'contenido');

            $table = $slide->createTableShape($cols);
            $table->setHeight($headerHeight + $rowHeight * $chunk->count())
                ->setWidth($tableWidth)
                ->setOffsetX($margin)
                ->setOffsetY($margin);

            $headerRow = $table->createRow();
            $headerRow->setHeight($headerHeight);
            $headers = $showHonorarios ? ['NOMBRE', 'RESUMEN', 'HONORARIOS'] : ['NOMBRE', 'RESUMEN'];

            foreach ($headers as $index => $label) {
                $cell = $headerRow->getCell($index);
                $cell->setWidth($colWidths[$index]);
                $cell->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF' . self::DARK_CARD));
                $this->styleTableCellBorders($cell);

                $run = $cell->createTextRun($label);
                $run->getFont()->setBold(true)->setSize(7)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);
                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            foreach ($chunk->values() as $entry) {
                $row = $table->createRow();
                $row->setHeight($rowHeight);

                $values = [
                    $entry->nombre ?? $entry->talent?->name ?? '',
                    $entry->resumen_corto ?? '',
                ];

                if ($showHonorarios) {
                    $monto = number_format((float) ($entry->honorarios ?? 0), 2);
                    $values[] = "\${$monto} MXN";
                }

                foreach ($values as $index => $value) {
                    $cell = $row->getCell($index);
                    $cell->setWidth($colWidths[$index]);
                    $cell->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF' . self::DARK_BG));
                    $this->styleTableCellBorders($cell);

                    $run = $cell->createTextRun($value);
                    // La columna de resumen (índice 1) usa fuente más chica que nombre/honorarios.
                    $fontSize = $index === 1 ? 6 : 8;
                    $run->getFont()->setSize($fontSize)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);
                }
            }
        }
    }

    protected function styleTableCellBorders($cell): void
    {
        $sides = [$cell->getBorders()->getLeft(), $cell->getBorders()->getRight(), $cell->getBorders()->getTop(), $cell->getBorders()->getBottom()];

        foreach ($sides as $border) {
            $border->setLineStyle(Border::LINE_SINGLE)
                ->setLineWidth(1)
                ->setColor(new Color('FF' . self::GOLD));
        }

        $cell->getActiveParagraph()->getAlignment()
            ->setMarginLeft(10)
            ->setMarginRight(10)
            ->setMarginTop(6)
            ->setMarginBottom(6);
    }

    // =========================================================================
    // CONTRAPORTADA
    // =========================================================================

    protected function addClosingSlide(PhpPresentation $presentation, Roster $roster): void
    {
        $slide = $presentation->createSlide();
        $this->addBackgroundImage($slide, 'presentaciones');

        $y = 45;

        // 1. Logos al 50% del ancho.
        $y = $this->drawLogosRow($slide, $roster, $y, 0.5, 40, 20) + 20;
        

        // 2. Separador.
        $y = $this->addCenteredImage($slide, 'images/separador.png', $y, 5) + 20;

        // 3. Texto de cierre propio del roster (outro_text), antes del texto fijo de condiciones.
        if ($roster->outro_text) {
            $outroShape = $slide->createRichTextShape()
                ->setHeight(36)
                ->setWidth(self::SLIDE_W - 100)
                ->setOffsetX(50)
                ->setOffsetY($y);
            $outroShape->setWrap(RichText::WRAP_SQUARE);
            $outroRun = $outroShape->createTextRun($roster->outro_text);
            $outroRun->getFont()->setBold(true)->setSize(12)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);
            $outroShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $y += 38;
        }

        // 4. Texto de condiciones, blanco, centrado, con más separación entre párrafos.
        $condicionesLineas = [
            "Talento sujeto a disponibilidad y se cotizará caso por caso con previo conocimiento del proyecto solicitado.",
            "Más requerimientos según el lugar de presentación y el aforo",
            "*Transporte *Hospedaje * Viáticos",
            "*Equipo de audio e iluminación de acuerdo al Rider.",
            "Depósito para bloquear la fecha del 50% a la firma del contrato y 50% restante 10 días antes de la presentación.",
            "Cualquier otro espectáculo no incluido, podrá ser cotizado a petición del cliente.",
        ];

        $condicionesShape = $slide->createRichTextShape()
            ->setHeight(190)
            ->setWidth(self::SLIDE_W - 100)
            ->setOffsetX(50)
            ->setOffsetY($y);
        $condicionesShape->setWrap(RichText::WRAP_SQUARE);

        foreach ($condicionesLineas as $index => $line) {
            $paragraph = $condicionesShape->createParagraph();
            $paragraph->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $run = $paragraph->createTextRun($line);
            $run->getFont()->setSize(8)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);

            // Párrafo en blanco después de cada línea (excepto la última) para separar visualmente.
            if ($index < count($condicionesLineas) - 1) {
                $spacer = $condicionesShape->createParagraph();
                $spacer->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $spacerRun = $spacer->createTextRun(' ');
                $spacerRun->getFont()->setSize(4)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);
            }
        }

        $y += 195;

        // 5. Datos de contacto, etiqueta en negritas, texto más chico y más espaciado entre líneas.
        $contacto = $roster->datos_contacto ?? [];
        foreach ($contacto as $item) {
            $line = $slide->createRichTextShape()
                ->setHeight(22)
                ->setWidth(self::SLIDE_W - 160)
                ->setOffsetX(80)
                ->setOffsetY($y);
            $line->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $label = strtoupper($item['tipo'] ?? '');
            $labelRun = $line->createTextRun("{$label}:  ");
            $labelRun->getFont()->setBold(true)->setSize(9)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);

            $valueRun = $line->createTextRun($item['valor'] ?? '');
            $valueRun->getFont()->setBold(false)->setSize(9)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);

            $y += 34;
        }

        $y += 12;

        // 5. Sitio web, dorado, grande, centrado.
        $webShape = $slide->createRichTextShape()
            ->setHeight(50)
            ->setWidth(self::SLIDE_W - 60)
            ->setOffsetX(30)
            ->setOffsetY($y);
        $webRun = $webShape->createTextRun('www.capetilloproducciones.mx');
        $webRun->getFont()->setBold(true)->setSize(22)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);
        $webShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    // =========================================================================
    // HELPERS: LOGOS, IMÁGENES CENTRADAS, FONDOS, RECORTES
    // =========================================================================

    protected function drawLogosRow(Slide $slide, ?Roster $roster, float $y, float $maxWidthRatio = 0.8, float $baseHeight = 50, float $baseGap = 25): float
    {
        $logos = $roster?->logos ?? [];
        if (empty($logos)) {
            return $y;
        }

        $paths = [];
        foreach ($logos as $logo) {
            $fullPath = Storage::disk('public')->path($logo);
            if (file_exists($fullPath)) {
                $paths[] = $fullPath;
            }
        }

        if (empty($paths)) {
            return $y;
        }

        $maxWidth = self::SLIDE_W * $maxWidthRatio;

        $widths = [];
        foreach ($paths as $path) {
            [$w, $h] = @getimagesize($path) ?: [1, 1];
            $widths[] = $h > 0 ? ($w * ($baseHeight / $h)) : $baseHeight;
        }

        $gapCount = max(count($paths) - 1, 0);
        $totalWidth = array_sum($widths) + $gapCount * $baseGap;

        $scale = $totalWidth > $maxWidth ? ($maxWidth / $totalWidth) : 1.0;

        $logoHeight = $baseHeight * $scale;
        $gap = $baseGap * $scale;
        $scaledWidths = array_map(fn ($w) => $w * $scale, $widths);

        $finalTotalWidth = array_sum($scaledWidths) + $gapCount * $gap;
        $x = (self::SLIDE_W - $finalTotalWidth) / 2;

        foreach ($paths as $index => $path) {
            $drawing = new DrawingFile();
            $drawing->setPath($path)
                ->setResizeProportional(false)
                ->setWidth($scaledWidths[$index])
                ->setHeight($logoHeight)
                ->setOffsetX($x)
                ->setOffsetY($y);
            $slide->addShape($drawing);
            $x += $scaledWidths[$index] + $gap;
        }

        return $y + $logoHeight;
    }

    protected function addCenteredImage(Slide $slide, string $publicRelativePath, float $y, float $height): float
    {
        $path = public_path($publicRelativePath);

        if (! file_exists($path)) {
            return $y;
        }

        [$w, $h] = @getimagesize($path) ?: [1, 1];
        $width = $h > 0 ? $w * ($height / $h) : $height;
        $x = (self::SLIDE_W - $width) / 2;

        $drawing = new DrawingFile();
        $drawing->setPath($path)
            ->setResizeProportional(false)
            ->setWidth($width)
            ->setHeight($height)
            ->setOffsetX($x)
            ->setOffsetY($y);
        $slide->addShape($drawing);

        return $y + $height;
    }

    protected function addBackgroundImage(Slide $slide, string $which = 'contenido'): void
    {
        $path = $this->getBackgroundImagePath($which);

        if (! $path) {
            $bg = new \PhpOffice\PhpPresentation\Slide\Background\Color();
            $bg->setColor(new Color('FF' . self::DARK_BG));
            $slide->setBackground($bg);
            return;
        }

        $bg = new \PhpOffice\PhpPresentation\Slide\Background\Image();
        $bg->setPath($path);
        $slide->setBackground($bg);
    }

    protected function getBackgroundImagePath(string $which): ?string
    {
        if (isset($this->backgroundImagePaths[$which])) {
            return $this->backgroundImagePaths[$which];
        }

        $filename = $which === 'presentaciones' ? 'fondo_presentaciones.jpg' : 'fondo_contenido.jpg';
        $sourcePath = public_path('images/' . $filename);

        if (! file_exists($sourcePath)) {
            return $this->backgroundImagePaths[$which] = null;
        }

        // Recorte "cover": llena por completo el lienzo vertical, recortando lo que sobre.
        $targetWidth = 1080;
        $targetHeight = (int) round($targetWidth * (self::SLIDE_H / self::SLIDE_W));

        $image = Image::decode(file_get_contents($sourcePath))->cover($targetWidth, $targetHeight);

        $tempPath = tempnam(sys_get_temp_dir(), 'roster_bg_') . '.jpg';
        file_put_contents($tempPath, (string) $image->encodeUsingFormat(\Intervention\Image\Format::JPEG, quality: 85));

        $this->tempFiles[] = $tempPath;

        return $this->backgroundImagePaths[$which] = $tempPath;
    }

    protected function resolveJpegPath(?string $storagePath, ?int $coverWidth = null, ?int $coverHeight = null): ?string
    {
        if (! $storagePath || ! Storage::disk('public')->exists($storagePath)) {
            return null;
        }

        // --- PRUEBA DE VELOCIDAD: usar el WebP original directo, sin decodificar/recortar/recodificar. ---
        // Nota: al saltarnos el cover(), la imagen no queda recortada a la proporción exacta del
        // recuadro, así que con setResizeProportional(false) puede verse estirada/deformada.
        return Storage::disk('public')->path($storagePath);

        // --- CÓDIGO ORIGINAL (recorte + conversión a JPEG) — descomentar si el WebP da problemas ---
        // $image = Image::decode(Storage::disk('public')->get($storagePath));
        //
        // if ($coverWidth && $coverHeight) {
        //     $image = $image->cover($coverWidth * 2, $coverHeight * 2, 'top');
        // }
        //
        // $tempPath = tempnam(sys_get_temp_dir(), 'roster_') . '.jpg';
        //
        // file_put_contents($tempPath, (string) $image->encodeUsingFormat(\Intervention\Image\Format::JPEG, quality: 85));
        //
        // $this->tempFiles[] = $tempPath;
        //
        // return $tempPath;
    }

    protected function cleanupTempFiles(): void
    {
        foreach ($this->tempFiles as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        $this->tempFiles = [];
    }
}