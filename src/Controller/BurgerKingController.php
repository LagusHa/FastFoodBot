<?php
/**
 * Created by PhpStorm.
 * User: gotohell
 * Date: 2020-09-07
 * Time: 18:29
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BurgerKingController extends AbstractController
{

    /**
     * @Route("/", name="dump")
     * @return Response
     * @throws \Exception
     */
    public function index(){
        $html = file_get_contents("https://burgerking.ru/bigboard/coupons");
        $crawler = new Crawler($html);

        $items = $crawler->filterXPath("//img[@class='coupon-img mt20']")->each(function (Crawler $node) {
             return $node->attr('src');
        });

        foreach ($items as $item) {
            $image = str_replace('..', 'https://burgerking.ru', $item);
            $path = '../public/assets/images/BurgerKing/' . basename($image);
            $file = file_get_contents($image);
            $insert = file_put_contents($path, $file);
            if (!$insert) {
                throw new \Exception('Failed to write image');
            }
        }


        $forRender['var'] = $items;
        return $this->render('dump.html.twig', $forRender);
    }
}