<?php

namespace App\Http\Controllers;

class PublicVCardController extends Controller
{
    public function alberto()
    {
        $vcard = "BEGIN:VCARD\r\n"
            . "VERSION:3.0\r\n"
            . "N:Capetillo;Alberto;;;\r\n"
            . "FN:Alberto Capetillo\r\n"
            . "ORG:Capetillo Producciones\r\n"
            . "TITLE:Director General y Mánager\r\n"
            . "TEL;TYPE=CELL:+525521179110\r\n"
            . "EMAIL:direcciongeneral@capetilloproducciones.mx\r\n"
            . "URL:https://capetilloproducciones.mx\r\n"
            . "END:VCARD\r\n";

        return response($vcard, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="alberto-capetillo.vcf"',
        ]);
    }

    public function paulina()
    {
        $vcard = "BEGIN:VCARD\r\n"
            . "VERSION:3.0\r\n"
            . "N:Capetillo;Paulina;;;\r\n"
            . "FN:Paulina Capetillo\r\n"
            . "ORG:Capetillo Producciones\r\n"
            . "TITLE:Directora de Ventas\r\n"
            . "TEL;TYPE=CELL:+525548706129\r\n"
            . "EMAIL:ventas@capetilloproducciones.mx\r\n"
            . "URL:https://capetilloproducciones.mx\r\n"
            . "END:VCARD\r\n";

        return response($vcard, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="paulina-capetillo.vcf"',
        ]);
    }

    public function carlos()
    {
        $vcard = "BEGIN:VCARD\r\n"
            . "VERSION:3.0\r\n"
            . "N:Jaime;Carlos;;;\r\n"
            . "FN:Carlos Jaime\r\n"
            . "ORG:Capetillo Producciones\r\n"
            . "TITLE:Ventas y Administración\r\n"
            . "TEL;TYPE=CELL:+525518365242\r\n"
            . "EMAIL:administracion@capetilloproducciones.mx\r\n"
            . "URL:https://capetilloproducciones.mx\r\n"
            . "END:VCARD\r\n";

        return response($vcard, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="carlos-jaime.vcf"',
        ]);
    }
}