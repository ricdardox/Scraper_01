<?php

namespace App\Http\Controllers;

use Symfony\Component\DomCrawler\Crawler;

class ScraperController extends Controller {

    //
    public function scraper($code) {
        $html = file_get_contents("http://scienti.colciencias.gov.co:8081/cvlac/visualizador/generarCurriculoCv.do?cod_rh=$code");
        $crawler = new Crawler($html);
        $result = ["status" => false, "message" => "No hay información"];
        try {
            $col0 = $crawler->filter('table table  tr td')->eq(0)->each(function (Crawler $node, $i) {
                return $node->text();
            });
            $col2 = $crawler->filter('table table  tr td')->eq(3)->each(function (Crawler $node, $i) {
                return $node->text();
            });
            if (count($col2) && strrpos($col0[0], "Categoría") !== false) {
                $col1 = $crawler->filter('table table  tr td')->eq(1)->each(function (Crawler $node, $i) {
                    return $node->text();
                });
                $result["status"] = true;
                $result["message"] = ["category" => $col1[0], "name" => $col2[0]];
            } else {
                if (count($col2)) {
                    $col2 = $col2[0];
                } else {
                    $col2 = "No se encontró información relacionada con el código $code";
                }
                $result["message"] = ["category" => "No registra ninguna categoria", "name" => $col2];
            }
        } catch (Exception $ex) {
            
        }
        return response()->json($result);
    }

}
