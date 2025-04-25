<?php
    class BingSearchRequest extends EngineRequest {
        public function get_request_url() {
            $query_encoded = str_replace("%22", "\"", urlencode($this->query));

            $results_language = $this->opts->language;
            $number_of_results = $this->opts->number_of_results;

            // TODO figure out how to not autocorrect
            $url = "https://www.bing.com/search?q=$query_encoded&first=" . ((10 * $this->page) + 1);

            // TODO language setting
            if (!is_null($results_language))
                $url .= "&lang=$results_language";

            return $url;
        }

        public function parse_results($response) {
            $results = array();
            $xpath = get_xpath($response);

            if (!$xpath)
                return $results;

            foreach($xpath->query("//ol[@id='b_results']//li") as $result) {
                $href_url = $xpath->evaluate(".//h2//a//@href", $result)[0];

                if ($href_url == null)
                    continue;

                $possible_url = $href_url->textContent;

                $possible_url_query = parse_url($possible_url, PHP_URL_QUERY);

                if ($possible_url_query == false)
                    continue;

                parse_str($possible_url_query, $possible_url);

                if (!array_key_exists('u', $possible_url))
                    continue;

                $possible_url = $possible_url['u'];
                
                if (str_starts_with($possible_url, "a1aHR0c"))
                {
                    // First two characters are irrelevant, strip for later
                    $possible_url = substr($possible_url, 2);
                }
                if (str_starts_with($possible_url, "aHR0c"))
                {
                    // Base64 "coded", extract and decode
                    $possible_url = str_replace('-', '+', $possible_url);
                    $possible_url = str_replace('_', '/', $possible_url);
                    $url = urldecode(base64_decode($possible_url, true));
                } else
                    $url = $possible_url;

                if (!empty($results) && array_key_exists("url", $results) && end($results)["url"] == $url->textContent)
                    continue;

                $title = $xpath->evaluate(".//h2//a", $result)[0];

                if ($title == null)
                    continue;

                $title = $title->textContent;

                $description = ($xpath->evaluate(".//div[contains(@class, 'b_caption')]//p", $result)[0] ?? null) ?->textContent ?? '';

                array_push($results,
                    array (
                        "title" => htmlspecialchars($title),
                        "url" =>  htmlspecialchars($url),
                        // base_url is to be removed in the future, see #47
                        "base_url" => htmlspecialchars(get_base_url($url)),
                        "description" =>  $description == null ?
                                          TEXTS["result_no_description"] :
                                          htmlspecialchars($description)
                    )
                );

            }
           return $results;
        }

    }
?>
