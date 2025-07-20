<?php
    class BingSearchRequest extends EngineRequest {
        public function get_request_url() {
            $query_encoded = str_replace("%22", "\"", urlencode($this->query));

            $results_language = $this->opts->language;
            $number_of_results = $this->opts->number_of_results;

            // NOTE Page(0,1)=1, Page(2)=9, Page(3+)=23..37..51..
            if ($this->page <= 1)
                $page = 1;
            elseif($this->page == 2)
                $page = 9;
            else
                $page = 9 + (($this->page - 2) * 14);
            $url = "https://www.bing.com/search?q=$query_encoded&first=$page&rdr=1";

            if (!is_null($results_language))
                $url .= "&srchlang=$results_language";

            // TODO Reconsider current safe-search implementation for granularity
            // NOTE Possible values are strict, demote (moderate, default), off
            if (isset($_COOKIE["safe_search"]))
                $url .= "&adlt=demote";

            return $url;
        }

        public function parse_results($response) {
            $results = array();
            $xpath = get_xpath($response);

            if (!$xpath)
                return $results;

            foreach($xpath->query("//ol[@id='b_results']/li") as $result) {
                $href_url = $xpath->evaluate(".//h2/a/@href", $result)[0];

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

                if (str_starts_with($url, "a1")) 
                  continue; // It's probably a Bing-relative link such as for video, skip it. 

                if (!empty($results) && array_key_exists("url", $results) && end($results)["url"] == $url->textContent)
                    continue;

                $title = $xpath->evaluate("./h2/a", $result)[0];

                if ($title == null)
                    continue;

                $title = $title->textContent;

                $description = ($xpath->evaluate("./div[contains(@class, 'b_caption')]/p", $result)[0] ?? null) ?->textContent ?? '';

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

            $didyoumean = $xpath->evaluate("//ol[@id='b_results']/li/div[contains(@class, 'sp_requery')]/a/strong")[0] ?? null;

            if (!is_null($didyoumean))
                array_push($results, array(
                    "did_you_mean" => $didyoumean->textContent
                ));

           return $results;
        }
    }
?>
