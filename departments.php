<?php

$csv = array();
if (($handle = fopen("departments.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $csv[] = $data;
    }
    fclose($handle);
}

unset($csv[0]);


if (!($BD = mysqli_connect('localhost', 'root', '', 'industrialbank'))) {
    echo "error BD";
    exit;
}
mysqli_set_charset($BD,'utf8');

foreach ($csv as $index=>$row) {

    $region = htmlspecialchars($row[0]);
    $city = htmlspecialchars($row[1]);
    $name = htmlspecialchars($row[2]);
    $address = htmlspecialchars($row[3]);
    $pos = strpos($address,",");
    $postcode = substr($address,0, $pos + 1);
    $address = trim(substr_replace($address, "", 0,$pos + 1));

    $type = ($row[5] == 'каса') ? 2 : 1;

    $phone1 = ''; $phone2 = ''; $phone3 = '';

    if ($type == 1) {
        if ($row[5]) $phone1 = $row[4]." ".$row[5];
        if ($row[6]) $phone2 = $row[4]." ".$row[6];
        if ($row[7]) $phone3 = $row[4]." ".$row[7];
    }

    $phones = ($phone1 ? $phone1.", " : "").($phone2 ? $phone2.", " : "").($phone3 ? $phone3.", " : "");
    $phones = trim($phones);
    $phones = trim($phones, ",");

    $r = mysqli_query($BD,"SELECT region_id FROM _regions WHERE title_ua='$region'");
    if (mysqli_num_rows($r)) {
        $regionId = mysqli_fetch_assoc($r);
        $regionId = $regionId['region_id'];

        if ($regionId) {
            $r = mysqli_query($BD,"SELECT city_id FROM _cities WHERE title_ua='$city' AND region_id='$regionId'");
            if ($r && mysqli_num_rows($r)) {
                $cityId = mysqli_fetch_assoc($r);
                $cityId = $cityId['city_id'];
            } else {
                mysqli_query($BD,"INSERT INTO _cities (`country_id`,`important`,`region_id`,`title_ru`,`title_ua`,`title_en`) VALUES ('2','0','$regionId','$city','$city','$city')");
                $r = mysqli_query($BD,"SELECT city_id FROM _cities WHERE title_ua='$city' AND region_id='$regionId'");
                if ($r && mysqli_num_rows($r)) {
                    $cityId = mysqli_fetch_assoc($r);
                    $cityId = $cityId['city_id'];
                }
            }

            if ($cityId) {
                $r = mysqli_query($BD,"SELECT id FROM pubor_branches_industrial WHERE name_ua='$name'");
                if (!$r or !mysqli_num_rows($r)) {
                    $r = "INSERT INTO `pubor_branches_industrial`(`lat`, `lng`, `name_ua`, `name_ru`, `name_en`, `city`, `address_ua`, `address_ru`, `address_en`, `description_ua`, `description_ru`, `description_en`, `phone`, `postcode`, `type`) VALUES ('00.00000000', '00.00000000', '$name', '$name', '$name', '$cityId','$address','$address','$address', '', '', '','$phones','$postcode','$type')";
                    mysqli_query($BD, $r);
                } else {
                    $branch = mysqli_fetch_assoc($r);
                    $branchId= $branch['id'];
//                    echo "UPDATE `pubor_branches_industrial` SET `city`='$cityId', `name_ua`='$name', `name_ru`='$name', `name_en`='$name', `address_ua`='$address', `address_ru`='$address', `address_en`='$address', `phone`='$phones', `postcode`='$postcode', `type`='$type' WHERE id='".$branchId."'".PHP_EOL;

                    $r = "UPDATE `pubor_branches_industrial` SET `city`='$cityId', `name_ua`='$name', `name_ru`='$name', `name_en`='$name', `address_ua`='$address', `address_ru`='$address', `address_en`='$address', `phone`='$phones', `postcode`='$postcode', `type`='$type' WHERE id='".$branchId."'";
                    mysqli_query($BD, $r);
                }
            }
        }
    }


//    $r = mysqli_query("SELECT c.city_id FROM _cities as c JOIN _regions as r ON c.region_id=r.region_id WHERE r.title_ua='Луганська область' AND c.title_ua='Луганськ'");

}
die;