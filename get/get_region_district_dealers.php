<?php



include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["user_id"];

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

        $sql_query6 = "SELECT * FROM users where privilege='ASM'";

        $result6 = $db->query($sql_query6) or die("Error :" . mysqli_error($db));

        $asm = array();
        while ($regiona = $result6->fetch_assoc()) {
            $asm[] = $regiona;
        }

        // print_r($itmes);

        $sql_query7 = "SELECT DISTINCT ownership FROM `dealers`";

        $result7 = $db->query($sql_query7) or die("Error :" . mysqli_error($db));

        $ownership = array();
        while ($region7 = $result7->fetch_assoc()) {
            $ownership[] = $region7;
        }

        $sql_query8 = "SELECT DISTINCT company FROM `dealers`";

        $result8 = $db->query($sql_query8) or die("Error :" . mysqli_error($db));

        $company = array();
        while ($region8 = $result8->fetch_assoc()) {
            $company[] = $region8;
        }

        $condition = '';
        if ($pre === 'ZM') {
            $condition = "dl.zm = $id";
        } elseif ($pre === 'TM') {
            $condition = "dl.tm = $id";
        } elseif ($pre === 'ASM') {
            $condition = "dl.asm = $id";
        } else {
            $condition = "1 = 1"; // Fallback condition if no matching privilege is found
        }

        $sql_query9 = "SELECT DISTINCT name FROM `dealers` as dl where $condition AND dl.indent_price = 1 ";

        $result9 = $db->query($sql_query9) or die("Error :" . mysqli_error($db));

        $company = array();
        while ($region9 = $result9->fetch_assoc()) {
            $dealers[] = $region9;
        }

        $data[] = array(
            'district' => json_encode($district),
            'city' => json_encode($city),
            'province' => json_encode($province),
            'region' => json_encode($region),
            'tm' => json_encode($TM),
            'asm' => json_encode($asm),
            'ownership' => json_encode($ownership),
            'company' => json_encode($company),
            'dealers' => json_encode($dealers),
        );

        echo json_encode($data);
    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>