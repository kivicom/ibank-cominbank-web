<?php
$lang = ($tpl->lang == 'uk')?'ua':'ru';

$select = '*';
//$branches = array();
$db = JFactory::getDbo();
$q = "SELECT b.*, c.title_$lang as city_name FROM `#__branches_".TEMPLATE."` as b JOIN _cities as c ON c.city_id=b.city";
$db->setQuery($q);
$branches = $db->loadObjectList();

/* cabinet_departments.php */
if (isset($_GET['xml'])) {

$rss_content = "<locations>";

foreach ($branches as $branch) {
    $rss_content .= '
<location>
    <id>'.sprintf("%'.03d\n", $branch->id).'</id>
    <coordinates latitude="'.$branch->lat.'" longitude="'.$branch->lng.'"/>
    <name>'.$branch->{'name_'.$lang}.'</name>
    <address>'.$branch->{'address_'.$lang}.'</address>
    <country id="UA">{DEPARTMENTS-UKRAINE}</country>
    <town id="'.$branch->city.'">'.$branch->city_name.'</town>
    <description>'.$branch->{'description_'.$lang}.'</description>
    <type>'.$branch->type.'</type>
    <attrs>
        <attr name="{DEPARTMENTS-INDEX}">'.$branch->postcode.'</attr>
    </attrs>
</location>        
    ';
}

    $rss_content .= "</locations>";
    $rss_content = strtr($rss_content, $I18N);

    header( "Content-type: application/xml; charset=utf-8" );
    echo $rss_content;

    exit();


}

$filter = isset($_GET['data']) ? $_GET['data'] : false;
$filterMarker = isset($filter['markers']) ? $filter['markers'] : false;

?>
<div class="branches-content">
    <div class="map-head clearfix">
        <div class="map-head__title col-md-3 col-sm-4 col-xs-12">
            <span>{MAP-HEAD-NEAREST-OFFICE}</span>
        </div>

        <div class="col-md-5 col-sm-4 col-xs-12">
            <div class="map-head__search">
                <input type="search" id="search" name="search">
                <button class="btn-search">
                    <i class="icons icons_search"></i>
                </button>
            </div>
        </div>
        <ul class="map-head__list col-md-4 col-sm-4 col-xs-12">
            <li class="active"><a href="#" data-val="">{MAP-HEAD-MAP}</a></li>
            <li class=""><a href="#" data-val="">{MAP-HEAD-LIST}</a></li>
        </ul>

        <form id="filter_location">
            <div class="map-head__select">
                <span>{MAP-HEAD-REGION}:</span>
                <select id="regions"><option value="">{MAP-HEAD-REGION-NOT-SELECTED}</option></select>
            </div>

            <div class="map-head__select">
                <span>{MAP-HEAD-LOCALITY}:</span>
                <select id="city"></select>
            </div>


            <div class="checkbox-grey-cont">
                <input class="type_departments checkbox-grey" id="id999" type="checkbox" checked="checked" value="1">
                <label for="id999">{MAP-HEAD-DEPARTMENTS}</label>

            </div>
            <div class="checkbox-grey-cont">
                <input class="type_departments checkbox-grey" id="id1000" type="checkbox" checked="checked" value="2">
                <label for="id1000">{MAP-HEAD-CASHIERS}</label>

            </div>
            <div class="checkbox-grey-cont">
                <input class="type_departments checkbox-grey" id="id1001" type="checkbox" checked="checked" value="3">
                <label for="id1001">{MAP-HEAD-ATMS}</label>
            </div>
        </form>


    </div>
</div>

<!--
<div class="mapInfoContent">
    <div class="mapInfoContent_t">Шмівське відділення номер 333</div>
    <div class="mapInfoContent_h">Адреса:</div>
    <div class="mapInfoContent_p">така-то адреса</div>
    <div class="mapInfoContent_p">такий-то графік роботи</div>
</div> -->
<?php
// локации отделений
$locations_list = "";


?>
<div class="map-toggle non-active">
    <table class="map-table hidden-xs">
        <thead>
        <tr>
            <th>{MAP-TABLE-NAME}</th>
            <th>{MAP-TABLE-ADDRESS}</th>
            <th>{MAP-TABLE-CONTACT-PHONES}</th>
            <th colspan="2">{MAP-TABLE-WORK-SCHEDULE}</th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($branches as $index => $branch){

            $branch_name = preg_replace( "/\r|\n/", "<br/>", $branch->{'name_'.$lang});
            $branch_address = preg_replace( "/\r|\n/", "<br/>", $branch->{'address_'.$lang});
            $branch_city = preg_replace( "/\r|\n/", "<br/>", $branch->{'city'});
            $branch_city_name = preg_replace( "/\r|\n/", "<br/>", $branch->{'city_name'});
            $branch_description = preg_replace( "/\r|\n/", "<br/>", $branch->{'description_'.$lang});
            $branch_descriptionT = preg_replace( "/\r|\n/", "", $branch_description );
            $branch_phone = preg_replace( "/\r|\n/", "<br/>", $branch->{'phone'});
            $branch_phoneT = preg_replace( "/\r|\n/", "", $branch_phone );

            $locations_list .= ($locations_list == "") ? "" : ",\n";
            $locations_list .= "{id:'".$branch->id."', lat:".$branch->lat.", lng:".$branch->lng.", name:'".$branch_name."', address:'".$branch_address."', city:'".$branch_city."', city_name:'".$branch_city_name."', description:'".$branch_descriptionT."', phone:'".$branch_phoneT."', postcode:'".$branch->postcode."', type:'".$branch->type."'}";
            $locations =  "[".$locations_list."]";

            ?>
            <tr data-id="<?=$index?>">
                <td>
                    <span><?=(strtr(html_entity_decode($branch_name), array("\n" => "<br/>")))?></span>
                </td>
                <td>
                    <span><?=(strtr(html_entity_decode($branch_address), array("\n" => "<br/>")))?></span>
                </td>
                <td>
                    <?=(strtr(html_entity_decode($branch_phone), array("\n" => "<br/>")))?>
                </td>
                <td>
                    <?=(strtr(html_entity_decode($branch_description), array("\n" => "<br/>")))?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

    <table class="map-table map-table_mobile hidden-lg hidden-md hidden-sm">
        <tr>
            <td>
                <span>Відділення №1</span>
                <span>м. Київ, пр. Голосіївський, 50</span>
                <span>(044) 257 8953</span>
                <span>(044) 524 6757</span>
                <span>{MAP-TABLE-CUSTOMER-SERVICE}:</span>
                <span>9:00 – 17:00</span>
                <span>{MAP-TABLE-CASHIER}:</span>
                <span>9:00 – 17:00</span>
                <span>{MAP-TABLE-EXCHANGE-OPERATIONS}:</span>
                <span>9:30 – 16:00</span>
                <span>{MAP-TABLE-BREAK}:</span>
                <span>13:00 – 14:00</span>
                <span>{MAP-TABLE-WEEKEND}:</span>
                <span>субота, неділя</span>
            </td>
        </tr>
    </table>
</div>


<div class="map_container map-toggle">
    <div id="map"></div>
</div>
<script>
    var markers = <?=$locations?>;
    var markersG = [];
    var map;
    var markersBounds;
    var firstSelectedMarker = false;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
            center: {lat: 49, lng: 32}
        });

        markersBounds = new google.maps.LatLngBounds();

        <?php
        if ($filterMarker !== false && is_array($filterMarker)):
            ?>
            let filterMarker = ['<?=implode("','",$filterMarker)?>'];
            <?php
        endif;
        ?>

        for (var i = 0; i < markers.length; i++) {
            var markerPosition = new google.maps.LatLng(markers[i].lat, markers[i].lng);

            markersBounds.extend(markerPosition);

            var imageMap = "<?=$tpl->path?>/images/icons/google-maps-red.png";
            var zindex = 0;
            if (typeof filterMarker !== "undefined") {
                if (filterMarker.indexOf(markers[i].id) !== -1) {
                    imageMap = "<?=$tpl->path?>/images/icons/google-maps-blue.png";
                    zindex = 1;
                    if (firstSelectedMarker === false) firstSelectedMarker = true;
                }
            }

            var marker = new google.maps.Marker({
                position: markerPosition,
                map: map,
                title: markers[i].name,
                icon: imageMap,
                zIndex: zindex
            });

            if (firstSelectedMarker === true) firstSelectedMarker = marker;


            var content = '<div class="mapInfoContent">' +
                '<div class="mapInfoContent_t">' +
                markers[i].name +
                '</div>' +
                '<div class="mapInfoContent_h">' +
                markers[i].city_name+', '+markers[i].address + ', ' + markers[i].postcode +
                '</div>' +
                '<div class="mapInfoContent_p">' +
                markers[i].description+
                '</div>' +
                '<div class="mapInfoContent_p">' +
                ''+markers[i].phone+'' +
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

        map.setCenter(markersBounds.getCenter(), map.fitBounds(markersBounds));

        if (firstSelectedMarker !== false) {
            google.maps.event.trigger(firstSelectedMarker, 'click', { });
        }
    }


    <?php
    $getCitiesOfBranches = $tpl -> getCitiesOfBranches();
    ?>

    var regions = <?=$getCitiesOfBranches?>;
    for(index in regions) {
        jQuery("#regions").append('<option value="'+index+'">'+regions[index].region+'</option>');
    }

    function filter_change() {
        if (markersG.length) {
            let region = jQuery("#regions").val();
            let city = jQuery("#city").val();
            let cities;

            if (regions[region] !== undefined) {
                cities = regions[region].cities;
                if (city) {
                    if (cities[city] !== undefined) {
                        let cities_ = {};
                        cities_[city] = cities[city];
                        cities = cities_;
                    }
                }
            }

            let listCities = [];
            for(index in markers) {
                let current = markers[index].city;
                if (!region || (cities[current] !== undefined)) {
                    listCities.push(parseInt(index));
                }
            }

            let sel_type = [];
            jQuery(".type_departments").each(function(){
                if (jQuery(this).prop("checked")) sel_type.push(jQuery(this).val());
            });

            if (sel_type.length) {
                let listCities_ = [];
                for(index in listCities) {
                    let getType = markers[listCities[index]]['type'];
                    if (sel_type.indexOf(getType) !== -1) {
                        listCities_.push(listCities[index]);
                    }
                }
                listCities = listCities_;
            }

            if (listCities.length) {
                let search = jQuery("#search").val().toLowerCase();
                if (search !== '') {
                    let listCities_ = [];
                    for(index in listCities) {
                        let name = markers[listCities[index]]['name'].toLowerCase();
                        let address = markers[listCities[index]]['address'].toLowerCase();
                        let description = markers[listCities[index]]['description'].toLowerCase();

                        if ((name.indexOf(search) != -1) || (address.indexOf(search) != -1) || (description.indexOf(search) != -1))
                            listCities_.push(listCities[index]);
                    }
                    listCities = listCities_;
                }
            }

            for(index in markersG) {
                if (listCities.indexOf(parseInt(index)) !== -1) {
                    markersG[index].setVisible(true);
                    jQuery(".map-table tr[data-id='"+index+"']").removeClass("hidden");
                } else {
                    markersG[index].setVisible(false);
                    jQuery(".map-table tr[data-id='"+index+"']").addClass("hidden");
                }
            }

            markersBounds = new google.maps.LatLngBounds();
            for(index in markersG) {
                var lat = markersG[index].getPosition().lat();
                var lng = markersG[index].getPosition().lng();
                var markerPosition = new google.maps.LatLng(lat, lng);
                markersBounds.extend(markerPosition);
            }
            map.setCenter(markersBounds.getCenter(), map.fitBounds(markersBounds));
        }
    }

    jQuery("#regions").change(function () {
        jQuery("#city option").remove();
        jQuery("#city").append('<option value="">{MAP-HEAD-REGION-NOT-SELECTED}</option>');
        if (regions[jQuery(this).val()] !== undefined) {
            var citiesList = regions[jQuery(this).val()].cities;

            for(index in citiesList) {
                jQuery("#city").append('<option value="'+index+'">'+citiesList[index]+'</option>');
            }
        }
        filter_change();
    });

    jQuery("#city").change(function () {
        filter_change();
    });

    jQuery("#search").keyup(function () {
        filter_change();
    });

    jQuery(".map-head__list").click(function () {
        filter_change();
    });

    jQuery(".type_departments").change(function () {
        filter_change();
    });

    jQuery("#regions").change();




    jQuery('.map-head__list li').on('click', function(e) {
        e.preventDefault();
        jQuery(this)
            .addClass('active')
            .siblings().removeClass('active');
        jQuery('.map-toggle')
            .removeClass('non-active')
            .eq(jQuery(this).index()).addClass('non-active');

        fixed_bottom();
    });




</script>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDPvTD5dgYk54DWp-KFBommey8BIvgxh-Y&callback=initMap&language=<?=strtolower($tpl->lang)?>">
</script>

<?php

?>
