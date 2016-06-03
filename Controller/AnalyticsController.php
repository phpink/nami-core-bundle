<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use PhpInk\Nami\CoreBundle\Util\PaginatedCollection;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Rest controller for analytics
 *
 * @Annotations\NamePrefix("nami_api_")
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @author  Geoffroy Pierret <geofrwa@yandex.com>
 */
class AnalyticsController extends AbstractController
{
    /**
     * Get product analytics.
     *
     * @ApiDoc(
     *   description = "Get the product analytics.",
     *   output = "PhpInk\Nami\CoreBundle\Util\PaginatedCollection",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", default="0", description="Offset from which to start listing items.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="10", description="How many items to return.")
     * @Annotations\QueryParam(name="orderBy", map=true, requirements="[a-zA-Z0-9-\.]+", description="Sort by fields")
     * @Annotations\QueryParam(name="filterBy", map=true, requirements="[a-zA-Z0-9-:\.\<\>\!\%+]+", description="Filters")
     *
     * @param ParamFetcherInterface $paramFetcher Param fetcher service
     *
     * @return array
     */
    public function getAnalyticsUsersAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkIsAdmin();
        /** @var \PhpInk\Nami\CoreBundle\Repository\UserRepository $repo */
        $repo = $this->getRepository('User');
        return $this->restView(
            new PaginatedCollection(
                $repo->getUserAnalytics(
                    $paramFetcher->get('offset'),
                    $paramFetcher->get('limit'),
                    $paramFetcher->get('orderBy'),
                    $paramFetcher->get('filterBy')
                ),
                'nami_api_get_analytics_products',
                null, function($item) {
                    return array(
                        'hits' => $item['hits'],
                        'product' => $item['0']
                    );
                }
            )
        );
    }

    /**
     * Get search analytics.
     *
     * @ApiDoc(
     *   description = "Get the search analytics.",
     *   output = "PhpInk\Nami\CoreBundle\Util\PaginatedCollection",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", default="0", description="Offset from which to start listing items.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="10", description="How many items to return.")
     * @Annotations\QueryParam(name="orderBy", map=true, requirements="[a-zA-Z0-9-\.]+", description="Sort by fields")
     * @Annotations\QueryParam(name="filterBy", map=true, requirements="[a-zA-Z0-9-:\.\<\>\!\%+]+", description="Filters")
     *
     * @param ParamFetcherInterface $paramFetcher Param fetcher service
     *
     * @return array
     */
    public function getAnalyticsSearchesAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkIsAdmin();
        /** @var \PhpInk\Nami\CoreBundle\Repository\AnalyticsRepository $repo */
        $repo = $this->getRepository('\PhpInk\Nami\CoreBundle\Model\Analytics\SearchAnalytics');
        return $this->restView(
            new PaginatedCollection(
                $repo->getSearchAnalytics(
                    $paramFetcher->get('offset'),
                    $paramFetcher->get('limit'),
                    $paramFetcher->get('orderBy'),
                    $paramFetcher->get('filterBy')
                ),
                'nami_api_get_analytics_searches',
                null, function($item) {
                    return array(
                        'hits' => $item['hits'],
                        'search' => $item['0']->getSearch()
                    );
                }
            )
        );
    }

    /**
     * Get user login analytics.
     *
     * @Annotations\Get("analytics/login")
     * @ApiDoc(
     *   description = "Get the login analytics for users.",
     *   output = "PhpInk\Nami\CoreBundle\Util\PaginatedCollection",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", default="0", description="Offset from which to start listing items.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="10", description="How many items to return.")
     * @Annotations\QueryParam(name="orderBy", map=true, requirements="[a-zA-Z0-9-\.]+", description="Sort by fields")
     * @Annotations\QueryParam(name="filterBy", map=true, requirements="[a-zA-Z0-9-:\.\<\>\!\%+]+", description="Filters")
     *
     * @param ParamFetcherInterface $paramFetcher Param fetcher service
     *
     * @return array
     */
    public function getAnalyticsLoginAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkIsAdmin();
        /** @var \PhpInk\Nami\CoreBundle\Repository\UserRepository $repo */
        $repo = $this->getRepository('User');
        return $this->restView(
            new PaginatedCollection(
                $repo->getLoginAnalytics(
                    $paramFetcher->get('offset'),
                    $paramFetcher->get('limit'),
                    $paramFetcher->get('orderBy'),
                    $paramFetcher->get('filterBy')
                ),
                'nami_api_get_analytics_login',
                null, function($item) {
                    return array(
                        'hits' => $item['hits'],
                        'reseller' => $item['0']
                    );
                }
            )
        );
    }

    /**
     * Get page views data.
     *
     * @Annotations\Get("analytics/pageviews")
     * @ApiDoc(
     *   description = "Get the page views from API.",
     *   output = "array",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", default="0", description="Offset from which to start listing items.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="10", description="How many items to return.")
     * @Annotations\QueryParam(name="orderBy", map=true, requirements="[a-zA-Z0-9-\.]+", description="Sort by fields")
     * @Annotations\QueryParam(name="filterBy", map=true, requirements="[a-zA-Z0-9-:\.\<\>\!\%+]+", description="Filters")
     *
     * @param ParamFetcherInterface $paramFetcher Param fetcher service
     *
     * @return array
     */
    public function getAnalyticsPageViewsAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkIsAdmin();
        /** @var \PhpInk\Nami\CoreBundle\Repository\AnalyticsRepository $repo */
        $repo = $this->getRepository('\PhpInk\Nami\CoreBundle\Model\Analytics\PageAnalytics');
        return $this->restView(
            new PaginatedCollection(
                $repo->getPageViewsAnalytics(
                    $paramFetcher->get('offset'),
                    $paramFetcher->get('limit'),
                    $paramFetcher->get('orderBy'),
                    $paramFetcher->get('filterBy')
                ),
                'nami_api_get_analytics_pageviews',
                null, function($items) {
                    $analytics = [
                        [
                            'label' => "Pages",
                            'color' => '#768294',
                            'data' => array()
                        ], [
                            'label' => "Sessions",
                            'color' => '#1f92fe',
                            'data' => array()
                        ]
                    ];

                    foreach ($items as $item) {
                        $monthName = $item->createdAt->format('M');
                        $analytics[0]['data'][] = array(
                            $monthName, $item['hits'],
                        );
                        $analytics[1]['data'][] = array(
                            $monthName, $item['0'],
                        );
                    }
                    return $analytics;
                }
            )
        );
    }

    /**
     * Get weather data.
     *
     * @Annotations\Get("analytics/weather")
     * @ApiDoc(
     *   description = "Get the weather previsions for 5 days.",
     *   output = "array",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @return array
     */
    public function getAnalyticsWeatherAction()
    {
        $this->checkIsAdmin();
        $weatherData = null;//$this->getCache()->get('weatherData');
        if (!is_array($weatherData)) {
            $weatherData = array();
            $ch = curl_init();
            curl_setopt(
                $ch, CURLOPT_URL,
                'http://api.openweathermap.org/data/2.5/forecast/daily?q=bordeaux,fr&cnt=5&lang=fr_FR&mode=json&APPID=57a282fbc5ecb6a287dfafca1e7ec3f9'
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            if ($data) {
                $result = json_decode($data);
                if ($result) {
                    foreach ($result->list as $dayWeather) {
                        $weather = $dayWeather->weather[0];
                        $weatherData[] = array(
                            'temp' => round($dayWeather->temp->day - 273.15),
                            'text' => $weather->main,
                            'icon' => array(
                                'big' => $this->getBigIcon($weather->icon, $weather->id),
                                'small' => $this->getSmallIcon($weather->icon, $weather->id)
                            ),
                            'humidity' => $dayWeather->humidity
                        );
                    }
                    //$this->getCache()->set('weatherData', $weatherData, 3600 * 24);
                }
            }
            curl_close($ch);
        }
        return $this->restView($weatherData);
    }

    private $bigIconMap;
    private function getBigIcon($icon, $id) {
        if (!$this->bigIconMap) {
            $this->bigIconMap = array(
                '01d' => "clear-day",
                '01n' => "clear-night",
                '02d' => "partly-cloudy-day",
                '02n' => "partly-cloudy-night",
                '04d' => "cloudy",
                '04n' => "cloudy",
                '10d' => "rain",
                '10n' => "rain",
                '13d' => "snow", //sleet
                '13n' => "snow", //sleet
                '01d' => "wind",
                '01n' => "wind",
                '50d' => "fog",
                '50n' => "fog"
            );
        }
        return array_key_exists($icon, $this->bigIconMap) ?
            $this->bigIconMap[$icon] : '';
    }

    private $iconMap;
    private function getSmallIcon($icon, $id) {
        if (!$this->iconMap) {
            $this->iconMap = array(
                // Thunderstorm
                '200' => array(
                    'd' => 'day-storm-showers',
                    'n' => 'night-alt-storm-showers'
                ),
                '201' => 200,
                '202' => 200,
                '210' => array(
                    'd' => 'day-lightning',
                    'n' => 'night-alt-lightning'
                ),
                '211' => array(
                    'd' => 'day-thunderstorm',
                    'n' => 'night-alt-thunderstorm'
                ),
                '212' => 211,
                '221' => 211,
                '230' => 210,
                '231' => 210,
                '232' => 210,
                // Drizzle
                '300' => array(
                    'd' => 'day-sprinkle',
                    'n' => 'night-sprinkle'
                ),
                '301' => 300,
                '302' => 300,
                '310' => array(
                    'd' => 'day-rain-mix',
                    'n' => 'night-rain-mix'
                ),
                '311' => 310,
                '312' => 310,
                '313' => 310,
                '314' => array(
                    'd' => 'day-showers',
                    'n' => 'night-showers'
                ),
                '321' => 314,
                // Rain
                '500' => 300,
                '501' => array(
                    'd' => 'day-rain',
                    'n' => 'night-rain'
                ),
                '502' => 501,
                '503' => 314,
                '504' => 314,
                '511' => array(
                    'd' => 'day-hail',
                    'n' => 'night-hail'
                ),
                '520' => 314,
                '521' => 314,
                '522' => 314,
                '531' => 314,
                // Snow
                '600' => array(
                    'd' => 'day-snow',
                    'n' => 'night-snow'
                ),
                '601' => 600,
                '602' => 600,
                '611' => 'snowflake-cold',
                '612' => 611,
                '615' => 600,
                '616' => 600,
                '620' => 600,
                '621' => 600,
                '622' => 600,
                // Atmosphere
                '701' => array(
                    'd' => 'day-fog',
                    'n' => 'night-fog'
                ),
                '711' => 'smoke',
                '721' => 'smog',
                '731' => 'dust',
                '741' => 701,
                '751' => 731,
                '761' => 731,
                '762' => 711,
                '771' => 'strong-wind',
                '781' => 'tornado',
                // Clouds
                '800' => array(
                    'd' => 'day-sunny',
                    'n' => 'night-clear'
                ),
                '801' => array(
                    'd' => 'day-sunny-overcast',
                    'n' => 'night-cloudy'
                ),
                '802' => 801,
                '803' => array(
                    'd' => 'day-cloudy',
                    'n' => 'cloudy'
                ),
                '804' => 803,
                // Extreme
                '900' => 781,
                '901' => 'meteor',
                '902' => 611,
                '903' => '',
                '904' => 'thermometer',
                '905' => 711,
                '906' => 611,
                // Additional
                '951' => 800,
                '952' => 800,
                '953' => 800,
                '954' => 'windy',
                '955' => 954,
                '956' => 954,
                '957' => 711,
                '958' => 711,
                '959' => 711,
                '960' => 781,
                '961' => 781,
                '962' => 781,
            );
        }
        $value = array_key_exists($id, $this->iconMap) ?
            $this->iconMap[$id] : '';
        if (is_int($value)) {
            $value = $this->iconMap{strval($value)};
        }
        if (is_array($value)) {
            $value = $value{$icon[2]};
        }
        return $value;
    }
}
