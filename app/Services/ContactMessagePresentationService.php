<?php

namespace App\Services;

use App\Models\ContactMessage;
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

class ContactMessagePresentationService
{
    protected const GOLD = 'DCA54A';
    protected const DARK_BG = '1A1A1A';
    protected const DARK_CARD = '262626';
    protected const WHITE = 'FFFFFF';
    protected const BLACK = '000000';
    protected const FONT = 'Calibri';

    protected const SLIDE_W = 960;
    protected const SLIDE_H = 540;

    protected array $tempFiles = [];
    protected array $backgroundImagePaths = [];

    protected array $tiposEvento = [
        'privado' => 'Privado',
        'corporativo' => 'Corporativo',
        'publico_masivo' => 'Público / Masivo',
        'con_venta_boletos' => 'Con venta de boletos',
        'social' => 'Social (boda / XV)',
        'gubernamental' => 'Gubernamental',
    ];

    public function generate(ContactMessage $contactMessage): string
    {
        $contactMessage->load('cotizacionTalents');

        $presentation = new PhpPresentation();
        $presentation->getLayout()
            ->setCX(self::SLIDE_W, \PhpOffice\PhpPresentation\DocumentLayout::UNIT_PIXEL)
            ->setCY(self::SLIDE_H, \PhpOffice\PhpPresentation\DocumentLayout::UNIT_PIXEL);

        $this->addCoverSlide($presentation, $contactMessage);
        $this->addEventDataSlide($presentation, $contactMessage);

        foreach ($contactMessage->cotizacionTalents as $entry) {
            $this->addInvestmentSlide($presentation, $entry);
        }

        $this->addRequirementsSlide($presentation, $contactMessage);
        $this->addNotesSlide($presentation, $contactMessage);

        $filename = 'cotizacion-' . $contactMessage->id . '-' . now()->timestamp . '.pptx';
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
    // 1. PORTADA
    // =========================================================================

    protected function addCoverSlide(PhpPresentation $presentation, ContactMessage $contactMessage): void
    {
        $slide = $presentation->getActiveSlide() ?: $presentation->createSlide();
        $this->setDarkBackground($slide);

        $this->addLogoImage($slide, 50, 55);

        $title = $slide->createRichTextShape()
            ->setHeight(60)
            ->setWidth(560)
            ->setOffsetX(60)
            ->setOffsetY(230);
        $run = $title->createTextRun($contactMessage->talent_name ?: 'Cotización de talento');
        $run->getFont()->setBold(true)->setSize(34)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);

        $subtitle = $slide->createRichTextShape()
            ->setHeight(40)
            ->setWidth(560)
            ->setOffsetX(60)
            ->setOffsetY(300);
        $subRun = $subtitle->createTextRun($contactMessage->ciudad ?: $contactMessage->estado_republica ?: '');
        $subRun->getFont()->setSize(18)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);

        $this->addStaticRightImage($slide, 'cotizacion_portada.jpg');
        $this->addFooter($slide);
    }

    // =========================================================================
    // 2. DATOS DEL EVENTO
    // =========================================================================

    protected function addEventDataSlide(PhpPresentation $presentation, ContactMessage $contactMessage): void
    {
        $slide = $presentation->createSlide();
        $this->setLightBackground($slide);
        $this->addSectionHeader($slide, 'DATOS DEL EVENTO');

        $rows = [
            ['CLIENTE', $contactMessage->name],
            ['FECHA', optional($contactMessage->fecha_evento)->format('d \d\e F \d\e\l Y') ?? 'Por confirmar'],
            ['CIUDAD', $contactMessage->ciudad ?: $contactMessage->estado_republica ?: '—'],
        ];

        $y = 130;
        foreach ($rows as [$label, $value]) {
            $labelShape = $slide->createRichTextShape()->setHeight(20)->setWidth(400)->setOffsetX(50)->setOffsetY($y);
            $labelRun = $labelShape->createTextRun($label);
            $labelRun->getFont()->setBold(true)->setSize(10)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);

            $valueShape = $slide->createRichTextShape()->setHeight(30)->setWidth(400)->setOffsetX(50)->setOffsetY($y + 20);
            $valueRun = $valueShape->createTextRun((string) $value);
            $valueRun->getFont()->setSize(16)->setColor(new Color('FF' . self::BLACK))->setName(self::FONT);

            $y += 70;
        }

        if ($contactMessage->venue) {
            $line = $slide->createRichTextShape()
                ->setHeight(1)
                ->setWidth(400)
                ->setOffsetX(50)
                ->setOffsetY($y);
            $line->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFCCCCCC'));
            $y += 20;

            $venueShape = $slide->createRichTextShape()->setHeight(40)->setWidth(400)->setOffsetX(50)->setOffsetY($y);
            $venueRun = $venueShape->createTextRun($contactMessage->venue);
            $venueRun->getFont()->setSize(13)->setColor(new Color('FF666666'))->setName(self::FONT);
        }

        $this->addStaticRightImage($slide, 'cotizacion_datos.jpg');
        $this->addFooter($slide, self::BLACK);
    }

    // =========================================================================
    // 3. INVERSIÓN POR TALENTO
    // =========================================================================

    protected function addInvestmentSlide(PhpPresentation $presentation, $entry): void
    {
        $slide = $presentation->createSlide();
        $this->setLightBackground($slide);
        $this->addSectionHeader($slide, "INVERSIÓN\n" . mb_strtoupper($entry->nombre ?? ''));

        // Caja negra con el precio
        $priceBox = $slide->createRichTextShape()
            ->setHeight(120)
            ->setWidth(470)
            ->setOffsetX(40)
            ->setOffsetY(130);
        $priceBox->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF' . self::DARK_BG));

        $label = $priceBox->getActiveParagraph();
        $label->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $labelRun = $label->createTextRun('PRESENTACIÓN EN VIVO');
        $labelRun->getFont()->setBold(true)->setSize(9)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);

        $monto = $entry->honorarios ? '$' . number_format((float) $entry->honorarios, 2) . ' MXN + IVA' : 'Precio a cotizar';
        $priceParagraph = $priceBox->createParagraph();
        $priceParagraph->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $priceRun = $priceParagraph->createTextRun($monto);
        $priceRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);

        $sublabelParagraph = $priceBox->createParagraph();
        $sublabelParagraph->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sublabelRun = $sublabelParagraph->createTextRun('Más rider, camerinos, catering y cuotas sindicales');
        $sublabelRun->getFont()->setBold(true)->setSize(9)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);

        $y = 270;

        if ($entry->incluye) {
            $y = $this->addLabeledBox($slide, 'INCLUYE', $entry->incluye, $y, 'FFF0F0F0', self::BLACK);
        }

        if ($entry->condiciones_pago) {
            $this->addLabeledBox($slide, 'CONDICIONES DE PAGO', $entry->condiciones_pago, $y, 'FFFFFFFF', self::BLACK, true);
        }

        $this->addTalentEntryImage($slide, $entry);
        $this->addFooter($slide, self::BLACK);
    }

    protected function addLabeledBox(Slide $slide, string $label, string $text, float $y, string $bgHex, string $textColorHex, bool $goldBorder = false): float
    {
        $height = 30 + (ceil(mb_strlen($text) / 60) * 16);

        $box = $slide->createRichTextShape()
            ->setHeight($height)
            ->setWidth(470)
            ->setOffsetX(40)
            ->setOffsetY($y);
        $box->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($bgHex));

        if ($goldBorder) {
            $box->getBorder()->setColor(new Color('FF' . self::GOLD))->setLineWidth(1);
        }

        $labelRun = $box->getActiveParagraph()->createTextRun($label);
        $labelRun->getFont()->setBold(true)->setSize(9)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);

        $textPara = $box->createParagraph();
        $textRun = $textPara->createTextRun($text);
        $textRun->getFont()->setSize(11)->setColor(new Color('FF' . $textColorHex))->setName(self::FONT);

        return $y + $height + 15;
    }

    // =========================================================================
    // 4. REQUERIMIENTOS
    // =========================================================================

    protected function addRequirementsSlide(PhpPresentation $presentation, ContactMessage $contactMessage): void
    {
        $hasContent = $contactMessage->requerimientos_operacion || $contactMessage->requerimientos_tecnicos;

        if (! $hasContent) {
            return;
        }

        $slide = $presentation->createSlide();
        $this->setLightBackground($slide);
        $this->addSectionHeader($slide, 'REQUERIMIENTOS');

        $y = 130;

        if ($contactMessage->requerimientos_tecnicos) {
            $y = $this->addNumberedBlock($slide, '01', 'Técnico', $contactMessage->requerimientos_tecnicos, $y);
        }

        if ($contactMessage->requerimientos_operacion) {
            $this->addNumberedBlock($slide, '02', 'Operación', $contactMessage->requerimientos_operacion, $y);
        }

        $this->addStaticRightImage($slide, 'cotizacion_requerimientos.jpg');
        $this->addFooter($slide, self::BLACK);
    }

    protected function addNumberedBlock(Slide $slide, string $number, string $title, string $text, float $y): float
    {
        $height = 30 + (ceil(mb_strlen($text) / 55) * 16);

        $box = $slide->createRichTextShape()
            ->setHeight($height)
            ->setWidth(470)
            ->setOffsetX(120)
            ->setOffsetY($y);
        $box->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFF0F0F0'));

        $titleRun = $box->getActiveParagraph()->createTextRun($title);
        $titleRun->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF' . self::BLACK))->setName(self::FONT);

        $textPara = $box->createParagraph();
        $textRun = $textPara->createTextRun($text);
        $textRun->getFont()->setSize(11)->setColor(new Color('FF555555'))->setName(self::FONT);

        $numberShape = $slide->createRichTextShape()
            ->setHeight($height)
            ->setWidth(60)
            ->setOffsetX(40)
            ->setOffsetY($y);
        $numberRun = $numberShape->createTextRun($number);
        $numberRun->getFont()->setBold(true)->setSize(28)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);

        return $y + $height + 20;
    }

    // =========================================================================
    // 5. NOTAS / CIERRE
    // =========================================================================

    protected function addNotesSlide(PhpPresentation $presentation, ContactMessage $contactMessage): void
    {
        $slide = $presentation->createSlide();
        $this->setDarkBackground($slide);

        $this->addLogoImage($slide, 30, 45);

        $dateShape = $slide->createRichTextShape()->setHeight(30)->setWidth(560)->setOffsetX(60)->setOffsetY(90);
        $dateRun = $dateShape->createTextRun('Fecha de esta cotización: ' . now()->translatedFormat('d \d\e F \d\e\l Y'));
        $dateRun->getFont()->setBold(true)->setSize(13)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);

        if ($contactMessage->notas) {
            $notasLabel = $slide->createRichTextShape()->setHeight(20)->setWidth(560)->setOffsetX(60)->setOffsetY(120);
            $notasLabelRun = $notasLabel->createTextRun('NOTAS');
            $notasLabelRun->getFont()->setBold(true)->setSize(11)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);

            $notasBody = $slide->createRichTextShape()->setHeight(160)->setWidth(560)->setOffsetX(60)->setOffsetY(150);
            $notasBody->setWrap(RichText::WRAP_SQUARE);
            $notasRun = $notasBody->createTextRun($contactMessage->notas);
            $notasRun->getFont()->setSize(11)->setColor(new Color('FFDDDDDD'))->setName(self::FONT);
        }

        if ($contactMessage->fecha_vigencia) {
            $vigenciaShape = $slide->createRichTextShape()->setHeight(20)->setWidth(560)->setOffsetX(60)->setOffsetY(330);
            $vigenciaRun = $vigenciaShape->createTextRun('Vigente hasta: ' . $contactMessage->fecha_vigencia->format('d/m/Y'));
            $vigenciaRun->getFont()->setBold(true)->setSize(11)->setColor(new Color('FF' . self::GOLD))->setName(self::FONT);
        }

        $y = 370;
        foreach (($contactMessage->datos_contacto ?? []) as $item) {
            $line = $slide->createRichTextShape()->setHeight(20)->setWidth(560)->setOffsetX(60)->setOffsetY($y);
            $label = strtoupper($item['tipo'] ?? '');
            $labelRun = $line->createTextRun("{$label}:  ");
            $labelRun->getFont()->setBold(true)->setSize(10)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);
            $valueRun = $line->createTextRun($item['valor'] ?? '');
            $valueRun->getFont()->setSize(10)->setColor(new Color('FF' . self::WHITE))->setName(self::FONT);
            $y += 24;
        }

        $this->addStaticRightImage($slide, 'cotizacion_cierre.jpg');
        $this->addFooter($slide);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    protected function addSectionHeader(Slide $slide, string $title): void
    {
        $header = $slide->createRichTextShape()->setHeight(70)->setWidth(560)->setOffsetX(40)->setOffsetY(20);
        $lines = explode("\n", $title);

        foreach ($lines as $index => $line) {
            $para = $index === 0 ? $header->getActiveParagraph() : $header->createParagraph();
            $run = $para->createTextRun($line);
            $run->getFont()->setBold(false)->setSize(26)->setColor(new Color('FF' . self::BLACK))->setName(self::FONT);
        }

        $goldLine = $slide->createRichTextShape()
            ->setHeight(4)
            ->setWidth(self::SLIDE_W)
            ->setOffsetX(0)
            ->setOffsetY(105);
        $goldLine->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF' . self::GOLD));
    }

    protected function addStaticRightImage(Slide $slide, string $filename): void
    {
        $path = public_path('images/' . $filename);

        if (! file_exists($path)) {
            return;
        }

        $imageWidth = 400;
        $drawing = new DrawingFile();
        $drawing->setPath($path)
            ->setResizeProportional(false)
            ->setWidth($imageWidth)
            ->setHeight(self::SLIDE_H)
            ->setOffsetX(self::SLIDE_W - $imageWidth)
            ->setOffsetY(0);
        $slide->addShape($drawing);
    }

    protected function addTalentEntryImage(Slide $slide, $entry): void
    {
        if (! $entry->imagen || ! Storage::disk('public')->exists($entry->imagen)) {
            return;
        }

        $imageWidth = 400;
        $drawing = new DrawingFile();
        $drawing->setPath(Storage::disk('public')->path($entry->imagen))
            ->setResizeProportional(false)
            ->setWidth($imageWidth)
            ->setHeight(self::SLIDE_H)
            ->setOffsetX(self::SLIDE_W - $imageWidth)
            ->setOffsetY(0);
        $slide->addShape($drawing);
    }

    protected function setDarkBackground(Slide $slide): void
    {
        $bg = new \PhpOffice\PhpPresentation\Slide\Background\Color();
        $bg->setColor(new Color('FF' . self::DARK_BG));
        $slide->setBackground($bg);
    }

    protected function setLightBackground(Slide $slide): void
    {
        $bg = new \PhpOffice\PhpPresentation\Slide\Background\Color();
        $bg->setColor(new Color('FFFFFFFF'));
        $slide->setBackground($bg);
    }

    protected function addImageBackground(Slide $slide, string $filename): void
    {
        $path = $this->getBackgroundImagePath($filename);

        if (! $path) {
            $this->setLightBackground($slide);
            return;
        }

        $bg = new \PhpOffice\PhpPresentation\Slide\Background\Image();
        $bg->setPath($path);
        $slide->setBackground($bg);
    }

    protected function getBackgroundImagePath(string $filename): ?string
    {
        if (isset($this->backgroundImagePaths[$filename])) {
            return $this->backgroundImagePaths[$filename];
        }

        $sourcePath = public_path('images/' . $filename);

        if (! file_exists($sourcePath)) {
            return $this->backgroundImagePaths[$filename] = null;
        }

        $targetWidth = 1600;
        $targetHeight = (int) round($targetWidth * (self::SLIDE_H / self::SLIDE_W));

        $image = Image::decode(file_get_contents($sourcePath))->cover($targetWidth, $targetHeight);

        $tempPath = tempnam(sys_get_temp_dir(), 'cotizacion_bg_') . '.jpg';
        file_put_contents($tempPath, (string) $image->encodeUsingFormat(\Intervention\Image\Format::JPEG, quality: 85));

        $this->tempFiles[] = $tempPath;

        return $this->backgroundImagePaths[$filename] = $tempPath;
    }

    protected function addLogoImage(Slide $slide, float $y, float $height = 55): float
    {
        $path = public_path('images/logo_grande_blanco.png');

        if (! file_exists($path)) {
            return $y;
        }

        [$w, $h] = @getimagesize($path) ?: [1, 1];
        $width = $h > 0 ? $w * ($height / $h) : $height;
        $x = 60;

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

    protected function addFooter(Slide $slide, string $colorHex = self::WHITE): void
    {
        $footer = $slide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(500)
            ->setOffsetX(40)
            ->setOffsetY(self::SLIDE_H - 35);
        $run = $footer->createTextRun('ventas@capetilloproducciones.mx · www.capetilloproducciones.mx');
        $run->getFont()->setSize(9)->setColor(new Color('FF' . $colorHex))->setName(self::FONT);
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