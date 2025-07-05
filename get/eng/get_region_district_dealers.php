<?php



include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];

if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT DISTINCT district FROM `dealers`;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $district = array();
        while ($district1 = $result1->fetch_assoc()) {
            $district[] = $district1;
        }


        $sql_query = "SELECT DISTINCT city FROM `dealers`";

        $result2 = $db->query($sql_query) or die("Error :" . mysqli_error($db));

        $city = array();
        while ($city1 = $result2->fetch_assoc()) {
            $city[] = $city1;
        }

        $sql_query2 = "SELECT DISTINCT province FROM `dealers`";

        $result3 = $db->query($sql_query2) or die("Error :" . mysqli_error($db));

        $province = array();
        while ($province1 = $result3->fetch_assoc()) {
            $province[] = $province1;
        }
        $sql_query3 = "SELECT DISTINCT region FROM `dealers`";

        $result4 = $db->query($sql_query3) or die("Error :" . mysqli_error($db));

        $region = array();
        while ($region1 = $result4->fetch_assoc()) {
            $region[] = $region1;
        }

        $sql_query5 = "SELECT * FROM users where privilege='TM'";

        $result5 = $db->query($sql_query5) or die("Error :" . mysqli_error($db));

        $TM = array();
        while ($regiont = $result5->fetch_assoc()) {
            $TM[] = $regiont;
        }

        $sql_query6 = "SELECT * FROM users where privilege='Eng'";

        $result6 = $db->query($sql_query6) or die("Error :" . mysqli_error($db));

        $asm = array();
        while ($regiona = $result6->fetch_assoc()) {
            $asm[] = $regiona;
        }

        // print_r($itmes);


        $data[] = array(
            'district' => json_encode($district),
            'city' => json_encode($city),
            'province' => json_encode($province),
            'region' => json_encode($region),
            'tm' => json_encode($TM),
            'asm' => json_encode($asm)
        );

        echo json_encode($data);
    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>