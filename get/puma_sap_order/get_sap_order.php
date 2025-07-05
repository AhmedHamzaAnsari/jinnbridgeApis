<?php
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
// $id=$_GET["id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $currnt_date = $_GET['to'];
        $tommorrow = $_GET['date'];
        // $currnt_date = '2021-11-17';
        $category_sql;
        $werehouse = $_SESSION['werehouse'];
        $zone = $_SESSION['zone'];
        $channel = $_SESSION['channel'];
        $category_sql = "SELECT distinct count(dc.name) as trip,dc.name as vehiclenames ,am.c_name as depot,am.uniqueId,am.active_time,am.status,am.end_time,am.sap_no,pos.vehicle_id as vehiclename  from ptoptrack.amreli_trippro as am join ptoptrack.devices as dc on am.uniqueId=dc.uniqueId join amreli_live_sap as als on als.sap_no = am.sap_no join positions as pos on pos.id = dc.latestPosition_id where am.date>'$currnt_date' and am.date<'$tommorrow'  and als.vehicle !=''  group by dc.name;";


        $resultset = mysqli_query($db, $category_sql) or die("database error:" . mysqli_error($db));
        $active_class = 0;
        $category_html = '';
        $product_html = '';
        $modal_zoom = '';
        while ($category = mysqli_fetch_assoc($resultset)) {
            $current_tab = "";
            $current_content = "";
            if (!$active_class) {
                $active_class = 1;
                $current_tab = 'active';
                $current_content = 'in active';
            }
            $category_html .= '<a class="nav-link ' . $current_tab . '  mb-3" id="v-line-pills-home-tab' . $category['vehiclename'] . '"
        data-toggle="pill" href="#v-line-pills-home' . $category['vehiclename'] . '" role="tab"
        aria-controls="v-line-pills-home' . $category['vehiclename'] . '" aria-selected="true">
        
        <div class="container-fluid my-3">
            <div class="row mb-3">
                <div class="col-md-3" style="">
                    <i class="fa fas fa-truck-moving"
                        style="font-size:18px"></i>
                </div>
                <div class="col-md-9 text-left "
                    style="margin: auto;font-weight: bold;color:#3e3ea7;">
                    ' . $category['vehiclename'] . '</div>
               
            </div>
            <div class="row mb-3">
                <div class="col-md-3" style="">
                    <i class="fa fas fa-warehouse"
                        style="font-size:18px"></i>
                </div>
                <div class="col-md-9 text-left "
                    style="margin: auto;font-weight: bold;color:#3e3ea7;">
                    ' . $category['depot'] . '</div>
               
            </div>
            <div class="row mb-3">
                <div class="col-md-3" style="">
                    <i class="fa fas fa-list-ol"
                        style="font-size:18px"></i>
                </div>
                <div class="col-md-9 text-left "
                    style="margin: auto;font-weight: bold;color:#3e3ea7;">
                    ' . $category['sap_no'] . '</div>
               
            </div>
            <div class="row  mb-3">
                <div class="col-md-3" style="">
                    <i class="fa fas fa-route" style="font-size:20px"></i>
                </div>
                <div class="col-md-9 text-left " style="margin: auto;">' . $category['trip'] . ' Trips
                </div>
                
                
            </div>
            
        </div>

    </a>';
            $product_html .= '<div class="tab-pane fade show  ' . $current_content . '" id="v-line-pills-home' . $category["vehiclename"] . '" role="tabpanel" aria-labelledby="v-line-pills-home-tab' . $category["vehiclename"] . '">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table mb-4" >
                        <thead>
                            <tr>
                                <th class="text-center">S.No</th>
                                <th>Start Time</th>
                                <th>Warehouse Name</th>
                                <th>Vehicle Name</th>
                                <th>Quantity</th>
                                <th>Sap no</th>
                                <th>Customer Name</th>
                                <th>On Map</th>
                                <th>Force Closed</th>
                                <th class="">Trip Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            $cat_id = $category["vehiclename"];

            $product_sql = "SELECT dc.uniqueId,dc.name,am.id,am.address,am.Latitude,am.longitude,am.status,am.date,am.uniqueId,am.product,am.quantity,am.sap_no,am.c_name,am.chanel,am.zone,am.active_time,am.end_time FROM ptoptrack.amreli_trippro as am join ptoptrack.devices as dc on am.uniqueId=dc.uniqueId join ptoptrack.positions as pos on pos.id=dc.latestPosition_id where am.date >='$currnt_date' and am.date<'$tommorrow' and pos.vehicle_id ='$cat_id' ";
            // echo $product_sql;

            $product_results = mysqli_query($db, $product_sql) or die("database error:" . mysqli_error($db));
            if (!mysqli_num_rows($product_results)) {
                $product_html .= '<tr >
                            <td class="text-center " colspan="9">No Trips Found</td></tr>';
            }
            while ($product = mysqli_fetch_assoc($product_results)) {
                if ($product["status"] === '8') {
                    $product_html .= ' <tr style="background-color:#FFF">
                                <td class="text-center">' . $product["id"] . '</td>
                                <td >' . $product["date"] . '</td>
                                <td>' . $product["product"] . '</td>
                                <td>' . $product["name"] . '</td>
                                <td>' . $product["quantity"] . '</td>
                                <td>' . $product["sap_no"] . '</td>
                                <td>' . $product["c_name"] . '</td>
                                <td><button type="button" disabled style="width: max-content;" class=" btn btn-outline-info btn-rounded mb-2" onclick="my_markers(markering' . $product["id"] . ',congignee_name' . $product["id"] . ');"> Focused
                                        On Map</button></td>
                                <td><button
                                        class=" btn btn-outline-danger btn-rounded mb-2" style="width: max-content;"
                                        data-toggle="modal" data-target="#zoomupModal' . $product["id"] . '">Forced
                                        Cancel</button></td>
                                        <td class="">';

                    $product_html .= '<button class=" btn btn-outline-danger btn-rounded mb-2" style="width: max-content;">Wrong Information</button>';


                    $product_html .= '</td>
                                    </tr>';

                } else if ($product["status"] === '1') {
                    $product_html .= ' <tr style="background-color:#FFF">
                                <td class="text-center">' . $product["id"] . '</td>
                                <td >' . $product["date"] . '</td>
                                <td>' . $product["product"] . '</td>
                                <td>' . $product["name"] . '</td>
                                <td>' . $product["quantity"] . '</td>
                                <td>' . $product["sap_no"] . '</td>
                                <td>' . $product["c_name"] . '</td>
                                <script>
                                var markering' . $product["id"] . ' = "' . $product["uniqueId"] . '";
                                var congignee_name' . $product["id"] . '="' . $product["product"] . '";
                                var lati' . $product["id"] . '="' . $product["Latitude"] . '";
                                var lngi' . $product["id"] . '="' . $product["longitude"] . '";
                                var active_time' . $product["id"] . '="' . $product["active_time"] . '";
                                var curr_time' . $product["id"] . '="' . $product["end_time"] . '";
                                </script>
                                <td><button type="button" class=" btn btn-outline-info btn-rounded mb-2" style="width: max-content;" onclick="my_markers_line(markering' . $product["id"] . ',congignee_name' . $product["id"] . ',lati' . $product["id"] . ',lngi' . $product["id"] . ',active_time' . $product["id"] . ',curr_time' . $product["id"] . ');"> Focused
                                        On Map</button></td>
                                <td><button
                                        class=" btn btn-outline-danger btn-rounded mb-2" style="width: max-content;"
                                        data-toggle="modal" data-target="#zoomupModal' . $product["id"] . '">Forced
                                        Cancel</button></td>
                                        <td class="">';

                    $product_html .= '<button class=" btn btn-outline-primary btn-rounded mb-2" style="width: max-content;">Completed</button>';


                    $product_html .= '</td>
                                    </tr>';


                } else if ($product["status"] === '3') {
                    $product_html .= ' <tr style="background-color:#FFF">
                                <td class="text-center">' . $product["id"] . '</td>
                                <td >' . $product["date"] . '</td>
                                <td>' . $product["product"] . '</td>
                                <td>' . $product["name"] . '</td>
                                <td>' . $product["quantity"] . '</td>
                                <td>' . $product["sap_no"] . '</td>
                                <td>' . $product["c_name"] . '</td>
                                <td><button type="button" disabled class=" btn btn-outline-info btn-rounded mb-2" style="width: max-content;" onclick="my_markers(markering' . $product["id"] . ',congignee_name' . $product["id"] . ');"> Focused
                                        On Map</button></td>
                                <td><button
                                        class=" btn btn-outline-danger btn-rounded mb-2" style="width: max-content;"
                                        data-toggle="modal" data-target="#zoomupModal' . $product["id"] . '">Forced
                                        Cancel</button></td>
                                        <td class="">';

                    $product_html .= '<button class=" btn btn-outline-danger btn-rounded mb-2" style="width: max-content;">Forced Close</button>';


                    $product_html .= '</td>
                                    </tr>';


                } else if ($product["status"] === '0') {
                    $curr_time1 = date("Y-m-d h:i:s");
                    $curr_time = date('Y-m-d H:i:s', strtotime($curr_time1));
                    $product_html .= ' <tr style="background-color:#FFF">
                                <td class="text-center">' . $product["id"] . '</td>
                                <td >' . $product["date"] . '</td>
                                <td>' . $product["product"] . '</td>
                                <td>' . $product["name"] . '</td>
                                <script>
                                var markering' . $product["id"] . ' = "' . $product["uniqueId"] . '";
                                var congignee_name' . $product["id"] . '="' . $product["product"] . '";
                                var lati' . $product["id"] . '="' . $product["Latitude"] . '";
                                var lngi' . $product["id"] . '="' . $product["longitude"] . '";
                                var active_time' . $product["id"] . '="' . $product["active_time"] . '";
                                var curr_time' . $product["id"] . '="' . $curr_time . '";
                                </script>
                                <td>' . $product["quantity"] . '</td>
                                <td>' . $product["sap_no"] . '</td>
                                <td>' . $product["c_name"] . '</td>
                                <td><button type="button" class=" btn btn-outline-info btn-rounded mb-2" style="width: max-content;" onclick="my_markers_line(markering' . $product["id"] . ',congignee_name' . $product["id"] . ',lati' . $product["id"] . ',lngi' . $product["id"] . ',active_time' . $product["id"] . ',curr_time' . $product["id"] . ');"> Focused
                                        On Map</button></td>
                                
                                <td><button
                                        class=" btn btn-outline-danger btn-rounded mb-2" style="width: max-content;"
                                        data-toggle="modal" data-target="#zoomupModal' . $product["id"] . '">Forced
                                        Cancel</button></td>
                                        <td class="">';

                    $product_html .= '<button class=" btn btn-outline-success btn-rounded mb-2" style="width: max-content;">On Trip</button>';


                    $product_html .= '</td>
                                    </tr>';

                } else {
                    $product_html .= ' <tr style="background-color:#FFF">
                                <td class="text-center">' . $product["id"] . '</td>
                                <td >' . $product["date"] . '</td>
                                <td>' . $product["product"] . '</td>
                                <td>' . $product["name"] . '</td>
                                <script>
                                var markering' . $product["id"] . ' = "' . $product["uniqueId"] . '";
                                var congignee_name' . $product["id"] . '="' . $product["product"] . '";
                                var lati' . $product["id"] . '="' . $product["Latitude"] . '";
                                var lngi' . $product["id"] . '="' . $product["longitude"] . '";
                                </script>
                                <td>' . $product["quantity"] . '</td>
                                <td>' . $product["sap_no"] . '</td>
                                <td>' . $product["c_name"] . '</td>
                                <td><button type="button" class=" btn btn-outline-info btn-rounded mb-2" style="width: max-content;" onclick="my_markers(markering' . $product["id"] . ',congignee_name' . $product["id"] . ',lati' . $product["id"] . ',lngi' . $product["id"] . ');"> Focused
                                        On Map</button></td>
                                
                                <td><button
                                        class=" btn btn-outline-danger btn-rounded mb-2" style="width: max-content;"
                                        data-toggle="modal" data-target="#zoomupModal' . $product["id"] . '">Forced
                                        Cancel</button></td>
                                        <td class="">';

                    $product_html .= '<button class=" btn btn-outline-warning  btn-rounded mb-2" style="width: max-content;">Pending</button>';


                    $product_html .= '</td>
                                    </tr>';

                }

                $modal_zoom .= '<div id="zoomupModal' . $product["id"] . '" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modal Header</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                            <p class="modal-text">' . $product["id"] . '</p>
                                            <div class="container my-4">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form action="forced_stop.php?trips__id=' . $product["id"] . '&comments=comments_add' . $product["id"] . '" method="post">
                                                        <div class="form-row mb-4">
                                                            <div class="form-group col-md-12">
                                                                <input type="hidden" class="form-control" id="trips__id' . $product["id"] . '" name="trips__id' . $product["id"] . '"
                                                                    placeholder="Enter Your Comments" value="' . $product["id"] . '" >
                                                            </div>
                                                            <div class="form-group col-md-12">
                                                                <label for="inputEmail4">Add Comments</label>
                                                                <input type="text" class="form-control" id="comments_add" name="comments_add' . $product["id"] . '"
                                                                    placeholder="Enter Your Comments" required>
                                                            </div>
                                                            
        
                                                        
        
                                                            
                                                            <div class="container">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <button onclick="myFunction()" class="btn btn-primary  font-weight-bold mx-2" name="Forced__stop" type="submit"
                                                                            style="float: right; ">SAVE</button>
                                                                        

                                                                    </div>
                                                                </div>
                                                            </div>
                                                                    
                                                            
                                                            
        
                                                            
        
                                                        
        
                                                            
        
                                                        </div>
        
                                                        
        
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                            </div>
                                            <div class="modal-footer md-button">
                                                <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
                                                <script>
                                                        function myFunction() {
                                                        confirm("Are you Confirm to Stop this trip ?");
                                                        }
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>';

            }
            $product_html .= ' 
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div><div class="clear_both"></div></div>';
        }

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>