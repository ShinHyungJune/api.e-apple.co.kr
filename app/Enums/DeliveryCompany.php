<?php

namespace App\Enums;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

enum DeliveryCompany: string
{
    case CJ = 'cj';
    case HANJIN = 'hanjin';
    case LOTTE = 'lotte'; case EPOST = 'epost';

    public function label(): string
    {
        return match ($this) {
            self::CJ => 'cj',
            self::HANJIN => '한진',
            self::LOTTE => '롯데',
            self::EPOST => '우체국',
        };
    }

    public static function getItems(): array
    {
        $results = [];
        foreach (self::cases() as $case) {
            $results[] = ['value' => $case->value, 'text' => $case->label()];
        }
        return $results;
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function trackingUrl($number): string
    {
        return match ($this) {
            self::CJ => 'https://trace.cjlogistics.com/web/detail.jsp?slipno=' . $number,

            //self::HANJIN => 'https://www.hanjin.com/kor/CMS/DeliveryMgr/WaybillSch.do?mCode=MN038&slipno=' . $number,
            self::HANJIN => 'https://www.hanjin.com/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&wblnum=' . $number . '&schLang=KR&wblnumText=',

            //self::LOTTE => 'https://www.lotteglogis.com/home/reservation/tracking/index?trackingNo=' . $number,
            self::LOTTE => 'https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo=' . $number,

            self::EPOST => 'https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?displayHeader=우체국&sid1=' . $number
        };
    }

    public function trackingApiUrl($number): string
    {
        return match ($this) {
            self::CJ => 'https://trace.cjlogistics.com/web/rest/selectWblNoState.do?slipno=' . $number,

            self::HANJIN => 'https://www.hanjin.com/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&wblnum=' . $number . '&schLang=KR&wblnumText=',

            self::LOTTE => 'https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo=' . $number,

            self::EPOST => 'https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?displayHeader=우체국&sid1=' . $number
        };
    }

    public function isDelivered(string $number): bool
    {
        return match ($this) {
            self::CJ => $this->checkCJ($number),
            self::HANJIN => $this->checkHANJIN($number),
            self::LOTTE => $this->checkLOTTE($number),
            self::EPOST => $this->checkEPOST($number),
            default => false,
        };
    }

    protected function checkCJ(string $number): bool
    {
        try {
            $response = Http::post($this->trackingApiUrl($number));
            $data = json_decode($response->body());
            $status = $data->data->stateOutput->basisSclsfCdNm ?? null;
            return $status === '배송완료';
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function checkHANJIN(string $number): bool
    {
        try {
            $html = Http::get($this->trackingApiUrl($number))->body();
            $crawler = new Crawler($html);
            $status = trim($crawler->filter('p.comm-sec strong')->text());

            return $status === '배송완료';
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function checkLOTTE(string $number): bool
    {
        try {
            $html = Http::get($this->trackingApiUrl($number))->body();
            $crawler = new Crawler($html);

            $row = $crawler->filter('table.tblH.mt60 tbody tr')->reduce(function (Crawler $tr) use ($number) {
                return trim($tr->filter('td')->eq(0)->text()) === $number;
            })->first();

            $deliveryResult = trim($row->filter('td')->eq(3)->text());

            return $deliveryResult === '배달완료';
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function checkEPOST(string $number): bool
    {
        try {
            $html = Http::get($this->trackingApiUrl($number))->body();
            $crawler = new Crawler($html);

            $row = $crawler->filter('th[scope="row"]')->reduce(function (Crawler $node) use ($number) {
                return trim($node->text()) === $number;
            })->first()->ancestors()->filter('tr')->first();

            $deliveryResult = trim($row->filter('td')->eq(4)->text());
            return $deliveryResult === '배달완료';

        } catch (\Exception $e) {
            return false;
        }
    }

    /*
    public function isDeliveryComplete($number): bool
    {
        switch ($this) {
            case self::CJ;
                $response = Http::post($this->trackingApiUrl($number));
                $data = json_decode($response->body(), true);
                $status = Arr::get($data, 'scanInfoOutput.stateOutput.basisSclsfCdNm');
                return $status === "배송완료";
                break;

            case self::HANJIN;
                $response = Http::get($this->trackingApiUrl($number));
                $html = $response->body();
                $crawler = new Crawler($html);
                $status = trim($crawler->filter('p.comm-sec strong')->text());

                return $status === "배송완료";
                break;

            case self::LOTTE;
                $response = Http::get($this->trackingApiUrl($number));
                $html = $response->body();

                $crawler = new Crawler($html);
                $deliveryResult = $crawler->filter('table.tblH.mt60 tbody tr')
                    ->reduce(function (Crawler $tr) use ($number) {
                        return trim($tr->filter('td')->eq(0)->text()) === $number;
                    })
                    ->first()
                    ->filter('td')->eq(3) // 4번째 <td> → 배달결과
                    ->text();
                return $deliveryResult === '배달완료';
                break;

            case self::EPOST:
                $response = Http::get($this->trackingApiUrl($number));
                $html = $response->body();

                $crawler = new Crawler($html);
                $targetRow = $crawler
                    ->filter('th[scope="row"]')
                    ->reduce(function (Crawler $node) use ($number) {
                        return trim($node->text()) === $number;
                    })
                    ->first()
                    ->ancestors()
                    ->filter('tr')
                    ->first();
                $deliveryResult = trim($targetRow->filter('td')->eq(4)->text());

                return $deliveryResult === "배달완료";
                break;
        }

        return false;
    }
    */

}
