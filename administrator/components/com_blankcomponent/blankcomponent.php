<?php
/**
* Author:	Omar Muhammad
* Email:	admin@omar84.com
* Website:	http://omar84.com
* Component:Blank Component
* Version:	3.0.0
* Date:		03/11/2012
* copyright	Copyright (C) 2012 http://omar84.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/

defined( '_JEXEC' ) or die( 'Restricted access' );


// Список всех городов
function getCities($select = '*')
{
    $result = array();
    $db = JFactory::getDBO();
    $q = "SELECT r.region_id as region_id, r.title_ru as region_name, c.city_id as city_id, c.title_ru as city_ru, c.title_ua as city_ua, c.title_en as city_en FROM `_cities` as c JOIN `_regions` as r ON c.region_id = r.region_id ORDER BY region_name, city_ru";
    $db->setQuery($q);
    $data_rows_assoc_list = $db->loadAssocList();

    foreach ($data_rows_assoc_list as $item) {
        $region_id = $item['region_id']."_";
        $city_id = $item['city_id']."_";

        if (!isset($result[$region_id]))
            $result[$region_id] = array('region'=>'','cities_ru'=>array(), 'cities_ua'=>array(), 'cities_en'=>array());

        $result[$region_id]['region'] = $item['region_name'];
        $result[$region_id]['cities_ru'][$city_id] = $item['city_ru'];
        $result[$region_id]['cities_ua'][$city_id] = $item['city_ua'];
        $result[$region_id]['cities_en'][$city_id] = $item['city_en'];
    }

    return json_encode($result, JSON_UNESCAPED_UNICODE);
}

$app = JFactory::getApplication();
$admin = $app->isAdmin();

$types = array(
    1 => "Отделение",
    2 => "Касса",
    3 => "Банкомат"
);

if($admin==1)
	{
?>
        <div>
            <?php
            $id = false;
            $CONFIG = \JFactory::getConfig();
            $GLOBALS['TEMPLATE'] = $CONFIG -> get('TEMPLATE');

            $theme = $GLOBALS['TEMPLATE'];

            $lang = "ru";

            if (isset($_POST['data'])) {
                $data = $_POST['data'];

                if (isset($data['id'])) {
                    $id = $data['id'];

                    $db = JFactory::getDBO();
                    if ($id) {
                        if (isset($_POST['delete']) && $_POST['delete'] == "1") {
                            //Удаление
                            $q = "DELETE FROM `#__branches_$theme` WHERE id=".$id;
                            $db->setQuery($q);
                            $db->query();
                        } else {
                            //Обновление

                            $query = '';
                            foreach ($data as $field => $value) {
                                if ($field == "id") continue;
                                if ($field == "city_name") continue;
                                if ($field == "city") {

                                    $value = trim($value, "_");

                                    $title_ru = isset($_POST['city_ru']) ? $_POST['city_ru'] : "";
                                    $title_ua = isset($_POST['city_ua']) ? $_POST['city_ua'] : "";
                                    $title_en = isset($_POST['city_en']) ? $_POST['city_en'] : "";

                                    if ($title_ru && $title_ua && $title_en) {
                                        $q = "UPDATE `_cities` SET `title_ru`='$title_ru', `title_ua`='$title_ua', `title_en`='$title_en' WHERE city_id=".$value;
                                        $db->setQuery($q);
                                        $db->query();
                                    }
                                }
                                $query .= "`".$field."`='".htmlspecialchars(strtr($value, array("\n"=>"<br/>")), ENT_QUOTES)."',";
                            }
                            $query = trim($query, ",");

                            $q = "UPDATE `#__branches_$theme` SET $query WHERE id=".$id;
                            $db->setQuery($q);
                            $db->query();
                        }
                    } else {
                        //Добавление

                        $fields = '';
                        $values = '';
                        foreach ($data as $field => $value) {
                            if ($field == "id") continue;
                            if ($field == "city") $value = trim($value, "_");
                            $fields .= "`".$field."`,";
                            $values .= "'".htmlspecialchars(strtr($value, array("\n"=>"<br/>")), ENT_QUOTES)."',";
                        }
                        $fields = "(".trim($fields, ",").")";
                        $values = "(".trim($values, ",").")";
                        $q = "INSERT INTO `#__branches_$theme` $fields VALUES $values";
                        $db->setQuery($q);
                        $db->query();
                    }
                }
            }






            $select = '*';
            //$branches = array();
            $db = JFactory::getDbo();
            $q = "SELECT b.*, c.title_ru as city_name FROM `#__branches_$theme` as b JOIN _cities as c ON c.city_id=b.city";
            $db->setQuery($q);
            $branches = $db->loadObjectList();

            // локации отделений
            $locations_list = "";

            foreach ($branches as $branch){
                $branch_name = preg_replace( "/\r|\n/", "<br/>", $branch->{'name_'.$lang});
                $branch_address = preg_replace( "/\r|\n/", "<br/>", $branch->{'address_'.$lang});
                $branch_city = preg_replace( "/\r|\n/", "<br/>", $branch->{'city_name'});
                $branch_description = preg_replace( "/\r|\n/", "<br/>", $branch->{'description_'.$lang});
                $branch_description = preg_replace( "/\r|\n/", "", $branch_description );
                $branch_phone = preg_replace( "/\r|\n/", "<br/>", $branch->{'phone'});
                $branch_phone = preg_replace( "/\r|\n/", "", $branch_phone );

                $locations_list .= ($locations_list == "") ? "" : ",\n";
                $locations_list .= "['".$branch->id."', ".$branch->lat.", ".$branch->lng.", '".$branch_name."', '".$branch_address."', '".$branch_city."', '".$branch_description."', '".$branch_phone."', '".$branch->postcode."']";
            }
            $locations =  "[\n".$locations_list."\n]";


            $assoc = array(
                'id' => array('name' => "ID", 'type' => 'string' ),
                'name_ua' => array('name' => "Название UA", 'type' => 'string'),
                'name_ru' => array('name' => "Название RU", 'type' => 'string'),
                'name_en' => array('name' => "Название EN", 'type' => 'string'),
                'city' => array('name' => "Город", 'type' => 'string'),
                'city_name' => array('name' => "Город", 'type' => 'string'),
                'lat' => array('name' => "Широта", 'type' => 'string'),
                'lng' => array('name' => "Долгота", 'type' => 'string'),
                'address_ua' => array('name' => "Адрес UA", 'type' => 'text'),
                'address_ru' => array('name' => "Адрес RU", 'type' => 'text'),
                'address_en' => array('name' => "Адрес EN", 'type' => 'text'),
                'description_ua' => array('name' => 'Описание UA', 'type' => 'text'),
                'description_ru' => array('name' => 'Описание RU', 'type' => 'text'),
                'description_en' => array('name' => 'Описание EN', 'type' => 'text'),
                'phone' => array('name' => 'Тел', 'type' => 'text'),
                'postcode' => array('name' => 'Индекс', 'type' => 'string'),
                'type' => array('name' => 'Тип', 'type' => 'radio')
            );
            ?>

            <style>
                .map_container {
                    margin: 50px auto;  width: 1250px;
                }

                .map {
                    width: 700px;display: inline-block; vertical-align: top;
                }

                #map {
                    width: 100%; height: 500px;
                }

                .edit_city {
                    margin-top: 50px;
                }

                .edit_block {
                    width: 540px; display: inline-block; vertical-align: top;
                }

                .form-horizontal {
                    font-size: 0;
                }

                .form-group {
                    margin-bottom: 10px;
                    display: inline-block;
                    vertical-align: top;
                    width: 250px;
                    margin-left: 20px;
                }

                .form-group label {
                    width: 100px !important;
                    display: inline-block !important;
                }

                .form-group div {
                    width: 130px !important;
                    display: inline-block !important;
                }

                .form-group input,.form-group textarea,.form-group select {
                    width: 100%;
                }

                .form-group.type-text div {
                    width: 400px !important;
                }

                .form-group.type-text {
                    width: 100%;
                }

                .form-group.type-radio {
                    width: 100%;
                    font-size: 13px;
                }

                .form-group.type-radio input {
                    width: 20px;
                    margin-top: -2px;
                }

                .form-group textarea {
                    height: 60px;
                }

                .icon-edit {
                    cursor: pointer;
                }

                .action.hidden {
                    display: none;
                }

                .table tr.active td {
                    background-color: #ffdb99;
                }
            </style>

            <input id="pac-input" class="controls" type="text" placeholder="Поиск" style="margin-top: 10px;">

            <div class="map_container map-toggle">
                <div class="map">
                    <div id="map"></div>
                    <div class="edit_city">
                        <table width="100%">
                            <tr>
                                <td width="33%">Город RU</td>
                                <td width="33%">Город UA</td>
                                <td width="33%">Город EN</td>
                            </tr>
                            <tr>
                                <td width="33%"><input type="text" name="city_ru"/></td>
                                <td width="33%"><input type="text" name="city_ua"/></td>
                                <td width="33%"><input type="text" name="city_en"/></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="edit_block">
                    <form class="form-horizontal form-edit" action="/administrator/index.php?option=com_blankcomponent" method="POST">
                        <input type="hidden" name="city_ru" />
                        <input type="hidden" name="city_ua" />
                        <input type="hidden" name="city_en" />

                        <?php
                        foreach ($assoc as $field => $item) {
                            if ($field == "city_name") continue;
                            if ($item['type'] == 'string') {
                                if ($field == "city") {
                                    ?>
                                    <div class="form-group type-region">
                                        <label class="col-sm-2 control-label">Область</label>
                                        <div class="col-sm-10">
                                            <select id="regions" name="region"></select>
                                        </div>
                                    </div>

                                    <div class="form-group type-<?=$item['type']?>">
                                        <label class="col-sm-2 control-label"><?=$item['name']?></label>
                                        <div class="col-sm-10">
                                            <select id="city" name="data[<?=$field?>]"></select>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="form-group type-<?=$item['type']?>">
                                        <label class="col-sm-2 control-label"><?=$item['name']?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="data[<?=$field?>]" class="form-control" placeholder="<?=$item['name']?>" <?=(($item['name'] == "ID") ? 'readonly="readonly"' : '')?>>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }

                            if ($item['type'] == 'text') {
                                ?>
                                <div class="form-group type-<?=$item['type']?>">
                                    <label class="col-sm-2 control-label"><?=$item['name']?></label>
                                    <div class="col-sm-10">
                                        <textarea name="data[<?=$field?>]" class="form-control"></textarea>
                                    </div>
                                </div>
                                <?php
                            }

                            if ($item['type'] == 'radio') {
                                ?>
                                <div class="form-group type-<?=$item['type']?>">
                                    <label class="col-sm-2 control-label"><?=$item['name']?></label>
                                    <?php
                                    foreach ($types as $id_ => $type) {
                                        ?>
                                        <div class="col-sm-10">
                                            <input type="radio" name="data[<?=$field?>]" value="<?=$id_?>" <?=(($id_ == 1) ? "checked='checked'" : "")?> /> <?=$type?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        }
                        ?>


                        <script>
                            var regions = <?=getCities()?>;
                            for(index in regions) {
                                jQuery("#regions").append('<option value="'+index+'">'+regions[index].region+'</option>');
                            }

                            jQuery("#regions").change(function () {
                                var citiesList = regions[jQuery(this).val()].cities_ru;
                                var citiesListUa = regions[jQuery(this).val()].cities_ua;
                                var citiesListEn = regions[jQuery(this).val()].cities_en;
                                jQuery("#city option").remove();
                                for(index in citiesList) {
                                    jQuery("#city").append('<option value="'+index+'" data-ru="'+citiesList[index]+'" data-ua="'+citiesListUa[index]+'" data-en="'+citiesListEn[index]+'">'+citiesList[index]+'</option>');
                                }
                                jQuery("#city").change();
                            });

                            jQuery("#city").change(function () {
                                var citiesList = jQuery(this).find("option:selected").data("ru");
                                var citiesListUa = jQuery(this).find("option:selected").data("ua");
                                var citiesListEn = jQuery(this).find("option:selected").data("en");

                                jQuery("input[name='city_ru']").val(citiesList);
                                jQuery("input[name='city_ua']").val(citiesListUa);
                                jQuery("input[name='city_en']").val(citiesListEn);
                            });


                            jQuery("#regions").change();
                        </script>

                        <div style="text-align: right">
                            <button type="button" class="btn btn-default" style="float:left" onclick="clearForm()">Создать</button>
                            <button type="button" class="btn btn-danger action-delete hidden" style="float:left; margin-left: 50px;" onclick="deleteBranch();">Удалить</button>
                            <button type="button" class="btn btn-primary action action-new" onclick="submitform()">Добавить</button>
                            <button type="button" class="btn btn-primary action action-update hidden" onclick="submitform()">Обновить</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                var markers = <?=$locations?>;
                var markersG = [];
                var map;

                function initMap() {
                    map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 6,
                        center: {lat: 49, lng: 32}
                    });

                    // Область показа маркеров
                    var markersBounds = new google.maps.LatLngBounds();

                    for (var i = 0; i < markers.length; i++) {
                        var markerPosition = new google.maps.LatLng(markers[i][1], markers[i][2]);

                        // Добавляем координаты маркера в область
                        markersBounds.extend(markerPosition);

                        // Создаём маркер
                        var marker = new google.maps.Marker({
                            position: markerPosition,
                            map: map,
                            title: markers[i][3],
                        });

                        var content = '<div class="mapInfoContent">' +
                            '<div class="mapInfoContent_t">' +
                            markers[i][3] +
                            '</div>' +
                            '<div class="mapInfoContent_h">' +
                            markers[i][5]+', '+markers[i][4] + ', ' + markers[i][8] +
                            '</div>' +
                            '<div class="mapInfoContent_p">' +
                            markers[i][6]+
                            '</div>' +
                            '<div class="mapInfoContent_p">' +
                            ''+markers[i][7]+'' +
                            '</div>' +
                            '</div>';
                        var infowindow = new google.maps.InfoWindow({
                            content: content
                        });


                        google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){
                            return function() {
                                infowindow.setContent(content);
                                infowindow.open(map,marker);
                            };
                        })(marker,content,infowindow));

                        markersG.push(marker);
                    }
                    // Центрируем и масштабируем карту
                    map.setCenter(markersBounds.getCenter(), map.fitBounds(markersBounds));

                    // This event listener will call addMarker() when the map is clicked.
                    map.addListener('click', function(event) {
                        addMarker(event.latLng);
                    });

                    // Create the search box and link it to the UI element.
                    var input = document.getElementById('pac-input');
                    var searchBox = new google.maps.places.SearchBox(input);
                    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                    // Bias the SearchBox results towards current map's viewport.
                    map.addListener('bounds_changed', function() {
                        searchBox.setBounds(map.getBounds());
                    });

                    searchBox.addListener('places_changed', function() {
                        var places = searchBox.getPlaces();

                        if (places.length == 0) {
                            return;
                        }

                        // Clear out the old markers.
                        markersG.forEach(function(marker) {
                            marker.setMap(null);
                        });
                        markersG = [];

                        // For each place, get the icon, name and location.
                        var bounds = new google.maps.LatLngBounds();
                        places.forEach(function(place) {
                            if (!place.geometry) {
                                console.log("Returned place contains no geometry");
                                return;
                            }
                            var icon = {
                                url: place.icon,
                                size: new google.maps.Size(71, 71),
                                origin: new google.maps.Point(0, 0),
                                anchor: new google.maps.Point(17, 34),
                                scaledSize: new google.maps.Size(25, 25)
                            };

                            // Create a marker for each place.
                            markersG.push(new google.maps.Marker({
                                map: map,
                                icon: icon,
                                title: place.name,
                                position: place.geometry.location
                            }));

                            if (place.geometry.viewport) {
                                // Only geocodes have viewport.
                                bounds.union(place.geometry.viewport);
                            } else {
                                bounds.extend(place.geometry.location);
                            }
                        });
                        map.fitBounds(bounds);
                    });
                }

                // Adds a marker to the map and push to the array.
                function addMarker(location) {
                    deleteMarkers();
                    var marker = new google.maps.Marker({
                        position: location,
                        map: map
                    });
                    var lat = marker.getPosition().lat();
                    var lng = marker.getPosition().lng();
                    jQuery(".form-horizontal input[name='data[lat]']").val(lat);
                    jQuery(".form-horizontal input[name='data[lng]']").val(lng);


                    markersG.push(marker);
                }

                // Sets the map on all markers in the array.
                function setMapOnAll(map) {
                    for (var i = 0; i < markersG.length; i++) {
                        markersG[i].setMap(map);
                    }
                }

                // Removes the markers from the map, but keeps them in the array.
                function clearMarkers() {
                    setMapOnAll(null);
                }

                // Shows any markers currently in the array.
                function showMarkers() {
                    setMapOnAll(map);
                }

                // Deletes all markers in the array by removing references to them.
                function deleteMarkers() {
                    clearMarkers();
                    markersG = [];
                }

                function clearForm() {
                    jQuery(".form-edit").find("input[type=text], textarea").val("");
                    jQuery(".action.action-new").removeClass("hidden");
                    jQuery(".action.action-update").addClass("hidden");
                    jQuery(".action-delete").addClass("hidden");
                }

                function deleteBranch() {
                    jQuery(".form-edit").append('<input type="hidden" name="delete" value="1" />');
                    jQuery(".form-edit").submit();
                }

                function submitform() {
                    jQuery("input[type='hidden'][name='city_ru']").val(jQuery("input[type='text'][name='city_ru']").val());
                    jQuery("input[type='hidden'][name='city_ua']").val(jQuery("input[type='text'][name='city_ua']").val());
                    jQuery("input[type='hidden'][name='city_en']").val(jQuery("input[type='text'][name='city_en']").val());
                    jQuery(".form-edit").submit();
                }

            </script>

            <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDPvTD5dgYk54DWp-KFBommey8BIvgxh-Y&callback=initMap&libraries=places">
            </script>

            <table class="table" style="position: relative">
                <?php
                foreach ($branches as $index => $branch) {
                    if (!$index) {
                        ?>
                        <thead class="thead-inverse">
                        <tr>
                            <?php
                            foreach ($branch as $field => $value) {?>
                                <th><?=(isset($assoc[$field]) ? $assoc[$field]['name'] : $field)?></th>
                            <?php
                            }
                            ?>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                    <?php
                    }
                    ?>
                    <tr class="<?=(($id == $branch->id) ? 'active' : '')?>">
                        <?php
                        foreach ($branch as $field => $value) {
                            if ($field == "type") {
                                ?>
                                <td data-field="<?= $field ?>"
                                    data-value="<?= htmlspecialchars(strtr($value, array("<br/>" => "\n", "&lt;br/&gt;" => "\n")), ENT_QUOTES) ?>"><?= $types[$value] ?></td>
                                <?php
                            } else {
                                ?>
                                <td data-field="<?= $field ?>"
                                    data-value="<?= htmlspecialchars(strtr($value, array("<br/>" => "\n", "&lt;br/&gt;" => "\n")), ENT_QUOTES) ?>"><?= html_entity_decode($value) ?></td>
                                <?php
                            }
                        }
                        ?>
                        <td>
                            <span class="icon-edit"></span>
                        </td>
                    </tr>
                <?php
                }
                ?>


                </tbody>
            </table>

            <script>
                jQuery(".icon-edit").click(function () {
                    let row = jQuery(this).closest("tr");
                    row.find("td").each(function () {
                        let field = jQuery(this).data("field");
                        let value = jQuery(this).data("value");
                        if (field !== undefined && value !== undefined) {
                            let element = jQuery(".form-edit").find("[name='data["+field+"]']");

                            if (field == "city") {
                                for(region in regions) {
                                    if (regions[region].cities_ru[value+"_"] !== undefined) {
                                        jQuery(".form-edit").find("[name='region']").val(region);
                                        jQuery("#regions").change();
                                        element.val(value+"_");
                                        jQuery("#city").change();
                                        break;
                                    }
                                }
                            } else if (field == "type") {
                                jQuery(".form-edit").find("[name='data["+field+"]'][value='"+value+"']").prop("checked", true);
                            } else {
                                element.val(value);
                            }
                        }
                    });
                    let dest = jQuery(".form-edit").offset().top - 100;
                    dest = dest < 0 ? 0 : dest;
                    jQuery("html, body").animate({scrollTop:dest}, 500);

                    jQuery(".action.action-new").addClass("hidden");
                    jQuery(".action.action-update").removeClass("hidden");
                    jQuery(".action-delete").removeClass("hidden");
                });

                if (jQuery(".table tr.active").length) {
                    let dest = jQuery(".table tr.active").offset().top - 100;
                    console.log(dest);
                    dest = dest < 0 ? 0 : dest;
                    jQuery("html, body").animate({scrollTop:dest}, 500);
                }

            </script>
        </div>
<?php
	}
else
	{

	jimport('joomla.application.component.controller');

	// Create the controller
	$controller = JControllerLegacy::getInstance('BlankComponent');
	// Perform the Request task
	$controller->execute(JRequest::getCmd('task'));

	// Redirect if set by the controller
	$controller->redirect();
	}

 ?>                                                   