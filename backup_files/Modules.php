<?php

final class Modules {

  public static function renderSearchForm($query = null) {
    $current_qualifier = $query ? $query->getQualifier() : null;
    $current_type = $query ? $query->getType() : null;
    $current_city = $query ? $query->getCity() : null;
    $html =
        '<div class="title-form">
          <form class="search-form table-form">
            <div class="table-form-column-short">Top 10</div>
            <div class="styled-select select-category table-form-column">
              <select id="search-qualifier">'
      .RenderUtils::renderSelectOptions(ListQualifiers::getConstants(), $current_qualifier)
              .'</select>
            </div>
            <div class="styled-select select-cuisine table-form-column">
              <select id="search-type">'
      .RenderUtils::renderSelectOptions(ListTypes::getConstants(), $current_type)
  .'</select>
            </div>
            <div class="table-form-column-short">spots in</div>
            <div class="styled-select select-location table-form-column">
              <select id="search-location">'
      .RenderUtils::renderSelectOptions(Cities::getConstants(), $current_city)
              .'</select>
            </div>
          </form></div>';
    return $html;

  }
}