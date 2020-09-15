<?php
/**
 * Created by PhpStorm.
 * User: gotohell
 * Date: 2020-09-08
 * Time: 19:22
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KfcController extends AbstractController
{
    /**
     * @Route("/kfc", name="kfc")
     * @return Response
     * @throws \Exception
     */
    public function kfc(){
        $html = file_get_contents("https://www.kfc.ru/coupons");
        $crawler = new Crawler($html);
        preg_match_all('/("image"."[0-9a-zA-Z.]+)|("title"."Купон[\sа-яА-я-0-9a-z]+)/ui', $html, $out,  PREG_PATTERN_ORDER);
        foreach ($out as $k => $v){
            $title[] = str_replace('"title":"Купон ', '', $v);
        }
        $items = $crawler->filterXPath("//div[@class='_2NyuN9wIxb _2863BpiS1v mr-32 mb-64']")->each(function (Crawler $node, $i) {
            return [
                $node->filterXPath("//div[@class='_2pr76I4WPm']")->text(),
                $node->filterXPath("//div[@class='_3POebZQSBG t-md c-description mt-16 pl-24 pr-24 condensed']")->text(),
                $node->filterXPath("//span[@class='fZklbU_aGI condensed']")->text(),
                $node->filterXPath("//span[@class='_1trEHSCHMh condensed c-primary bold']")->text(),
            ];
        });
        foreach ($items as $item){
            $titleCoupon[] = $item[0];
        }
        $result = array_uintersect($title[0], $titleCoupon, "strcasecmp");

        foreach ($title[0] as $key => $value){
            if (in_array($value, $result)){
                ++$key;
                $image[] = $title[0][$key];
                $imageName = str_replace('"image":"', '', $image);
            }
        }
        $imagePath = 'https://s82079.cdn.ngenix.net/.png?dw=230&dh=230';
        foreach ($imageName as $item){
            $imageNo = str_replace('.png', $item, $imagePath);
            $path = '../public/assets/images/KFC/' . md5($imageNo).'.png';
            $file = file_get_contents($imageNo);
            $insert = file_put_contents($path, $file);
            if (!$insert) {
                throw new \Exception('Failed to write image');
            }
        }

        $forRender['var'] = $imageName;
        return $this->render('dump.html.twig', $forRender);
    }

    public function generateImage(){
        //TODO сделать генерацию купонов. У КФС картинки и текст отдельно
    }
}