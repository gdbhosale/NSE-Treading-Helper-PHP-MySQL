<?php
$phone = "Phone number not given";
//print_r($user_info);

if($user_info['phone'] != "") {
	$phone = $user_info['phone'];
}

?>
<!-- Services Section -->
<section id="acc_header" class="lowmargin">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center" data-step="1" data-intro="Jai Shri Mataji ! Here lies information about you.">
                <a id="pageLink" href=""><h2 class="section-heading"><?php echo $user_info['full_name']; ?></h2></a>
                <h3 class="section-subheading text-muted" style="margin-bottom:10px;">
                    <ol class="breadcrumb">
                        <li class="cl-white"><?php echo $user_info['email']; ?> | <?php echo $phone; ?></li>
                    </ol>
                </h3>
            </div>
        </div>
    </div>
</section>

<section id="acc_info" class="bg-light-gray lowmargin">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 align-left" data-step="2" data-intro="Complete your profile...">
            	<?php
				if($user_info['phone'] == "") {
					?>
                    <h3>Complete your profile</h3>
                    <div class="col-lg-10 align-left" style="padding-left:0px;">
					<form id="userDetailsForm">
                        <div class="form-group">
                            <label for="userPhone">Phone number:</label>
                            <input type="text" class="form-control" id="userPhone" name="userPhone" placeholder="e.g. +918888888888" value="">
                        </div>
    				    <button type="submit" class="btn btn-primary">Save</button>
					</form>
                    </div>
                    <script type="text/javascript">
                        $(function() {
                            validator = $("#userDetailsForm").validate({
                                rules: {userPhone: {required:true,minlength:10}},
                                messages: {},
                                submitHandler: function(form) {
                                    $.ajax({
                                        url: base_url+"/home/completeProfile",
                                        type: "POST",
                                        data: $(form).serialize(),
                                        success: function( data ) {
                                            console.log(data);
                                            data = JSON.parse(data);
                                            if(data.status == "SUCCESS") {
                                                window.location.href = data.redirect_url;
                                            }
                                        }
                                    });
                                    return false;
                                }
                            });
                        });
                    </script>
					<?php
				} else {
                    $quote = $this->center_model->get_random_quote();
                    ?>
                    <br>
                    <img src="<?php echo $this->base_url; ?>/images/shri-mataji-1.jpg" style="max-width:100%;margin-bottom:20px;">
                    <p><?php echo $quote['english']; ?></p>
                    <p style="margin:0px;text-align:right">- <?php echo $quote['place_time']; ?></p>
                    <?php
                }
            	?>
            </div>
            <div class="col-lg-8 align-left">
                <h2>Centers <a class="pull-right btn btn-sm btn-primary" href="<?php echo $base_url; ?>/centers/add" data-step="3" data-intro="Add new meditation center"><i class="fa fa-plus"></i> </a> <p>Added / Edited by me</p> </h2>
                
                <table class="table table-bordered" data-step="4" data-intro="Center added by you.">
                	<thead>
                		<tr>
	                		<th>id</th>
                            <th>Type</th>
	                		<th>Name</th>
	                		<th>Remarks</th>
	                		<th>Status</th>
	                		<th>Action</th>
                		</tr>
                	</thead>
                	<tbody>
                        <?php
                        //print_r($my_centers);
                        foreach ($my_centers as $center) {
                            $transs = json_decode($center['transactions']);
                            $operation = "";
                            
                            foreach ($transs as $trans) {
                                if($trans->req_type == "CENTER_ADD") {
                                    $operation .= "Center added on <span title='".$trans->time_trans."' data-toggle='tooltip' data-placement='top'>".tsFormat($trans->time_trans, "M d, Y")."</span><br>\n";
                                } else if($trans->req_type == "CENTER_EDIT_TEMP") {
                                    $operation .= "Temp. Center edited on <span title='".$trans->time_trans."' data-toggle='tooltip' data-placement='top'>".tsFormat($trans->time_trans, "M d, Y")."</span><br>\n";
                                } else if($trans->req_type == "CENTER_EDIT") {
                                    $operation .= "Center edited on <span title='".$trans->time_trans."' data-toggle='tooltip' data-placement='top'>".tsFormat($trans->time_trans, "M d, Y")."</span><br>\n";
                                } else if($trans->req_type == "CENTER_DELETE") {
                                    $operation .= "Center deletion request on <span title='".$trans->time_trans."' data-toggle='tooltip' data-placement='top'>".tsFormat($trans->time_trans, "M d, Y")."</span><br>\n";
                                }
                            }

                            $req_type = "";
                            if($center['req_type'] == "CENTER_ADD") {
                                $req_type = '<i class="fa fa-plus-circle cl-green" title="New Center Added" data-toggle="tooltip" data-placement="top"></i>';
                            } else if($center['req_type'] == "CENTER_EDIT_TEMP") {
                                $req_type = '<i class="fa fa-pencil-square cl-yellow" title="Temporary Center Edited" data-toggle="tooltip" data-placement="top"></i>';
                            } else if($center['req_type'] == "CENTER_EDIT") {
                                $req_type = '<i class="fa fa-pencil-square cl-yellow" title="Center Edited" data-toggle="tooltip" data-placement="top"></i>';
                            } else if($center['req_type'] == "CENTER_DELETE") {
                                $req_type = '<i class="fa fa-times-circle cl-red" title="Center deletion request" data-toggle="tooltip" data-placement="top"></i>';
                            }
                            

                            $status = "";
                            if($center['AUTH'] == "YES") {
                                $status = '<i class="fa fa-check-circle cl-green" title="Autheticated" data-toggle="tooltip" data-placement="top"></i>';
                            } else {
                                $status = '<i class="fa fa-times-circle cl-red" title="Not Autheticated" data-toggle="tooltip" data-placement="top"></i>';
                            }

                            $center_link = "";
                            $center_title = "";
                            if($center['AUTH'] == "NOT") {
                               $center_link = $this->base_url."/centers/temp_view/".$center['id'];
                               $center_title = "Temporary View";
                            } else {
                                $center_link = $this->base_url."/centers/".$center['country']."/".$center['state']."/".$center['district']."/".$center['id_old'];
                                $center_title = "Center View";
                            }
                            ?>
                            <tr>
                                <td align="center"><?php echo $center['id']; ?></td>
                                <td align="center"><?php echo $req_type; ?></td>
                                <td style="max-width:150px;"><a href="<?php echo $center_link; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $center_title; ?>"><?php echo $center['place']; ?></a></td>
                                <td style="font-size:11px;"><?php echo $operation; ?></td>
                                <td align="center"><?php echo $status; ?></td>
                                <td>
                                    <?php
                                    if($center['AUTH'] == "NOT") {
                                        if($center['req_type'] != "CENTER_DELETE") {
                                            if($center['id_old'] == 0) {
                                                ?><a href="<?php echo $this->base_url."/centers/temp_edit/".$center['id']; ?>" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" title="Edit before authentication"><i class="fa fa-edit"></i></a><?php
                                            } else {
                                                ?><a href="<?php echo $this->base_url."/centers/temp_edit/".$center['id']; ?>" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" title="Edit Existing Center Request"><i class="fa fa-edit"></i></a><?php
                                            }
                                        }
                                        ?> <a class="btn delCReq btn-danger btn-xs" crid="<?php echo $center['id']; ?>" data-toggle="tooltip" data-placement="top" title="Delete before authentication"><i class="fa fa-times"></i></a><?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                	</tbody>
				</table>
                <br><br>
                <?php
                if(isset($centers_auth)) {
                ?>
                <h2>Centers <p>Authentication</p> </h2>
                <table class="table table-bordered" data-step="5" data-intro="Centers to be authenticated by you.">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($centers_auth as $center) {
                            $transs = json_decode($center['transactions']);
                            $operation = "";
                            foreach ($transs as $trans) {
                                if($trans->req_type == "CENTER_ADD") {
                                    $operation .= "Center added on <span title='".$trans->time_trans."' data-toggle='tooltip' data-placement='top'>".tsFormat($trans->time_trans, "M d, Y")."</span><br>\n";
                                } else if($trans->req_type == "CENTER_EDIT_TEMP") {
                                    $operation .= "Temp. Center edited on <span title='".$trans->time_trans."' data-toggle='tooltip' data-placement='top'>".tsFormat($trans->time_trans, "M d, Y")."</span><br>\n";
                                } else if($trans->req_type == "CENTER_EDIT") {
                                    $operation .= "Center edited on <span title='".$trans->time_trans."' data-toggle='tooltip' data-placement='top'>".tsFormat($trans->time_trans, "M d, Y")."</span><br>\n";
                                } else if($trans->req_type == "CENTER_DELETE") {
                                    $operation .= "Center deletion request on <span title='".$trans->time_trans."' data-toggle='tooltip' data-placement='top'>".tsFormat($trans->time_trans, "M d, Y")."</span><br>\n";
                                }
                            }

                            $req_type = "";
                            if($center['req_type'] == "CENTER_ADD") {
                                $req_type = '<i class="fa fa-plus-circle cl-green" title="New Center Added" data-toggle="tooltip" data-placement="top"></i>';
                            } else if($center['req_type'] == "CENTER_EDIT_TEMP") {
                                $req_type = '<i class="fa fa-pencil-square cl-yellow" title="Temporary Center Edited" data-toggle="tooltip" data-placement="top"></i>';
                            } else if($center['req_type'] == "CENTER_EDIT") {
                                $req_type = '<i class="fa fa-pencil-square cl-yellow" title="Center Edited" data-toggle="tooltip" data-placement="top"></i>';
                            } else if($center['req_type'] == "CENTER_DELETE") {
                                $req_type = '<i class="fa fa-times-circle cl-red" title="Center deletion request" data-toggle="tooltip" data-placement="top"></i>';
                            }

                            $status = "";
                            if($center['AUTH'] == "YES") {
                                $status = '<i class="fa fa-check-circle cl-green" title="Autheticated" data-toggle="tooltip" data-placement="top"></i>';
                            } else {
                                $status = '<i class="fa fa-times-circle cl-red" title="Not Autheticated" data-toggle="tooltip" data-placement="top"></i>';
                            }

                            $center_link = "";
                            $center_title = "";
                            if($center['AUTH'] == "NOT") {
                               $center_link = $this->base_url."/centers/temp_view/".$center['id'];
                               $center_title = "Temporary View";
                            } else {
                                $center_link = $this->base_url."/centers/".$center['country']."/".$center['state']."/".$center['district']."/".$center['id_old'];
                                $center_title = "Center View";
                            }
                            ?>
                            <tr>
                                <td align="center"><?php echo $center['id']; ?></td>
                                <td align="center"><?php echo $req_type; ?></td>
                                <td style="max-width:150px;"><a href="<?php echo $center_link; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $center_title; ?>"><?php echo $center['place']; ?></a></td>
                                <td style="font-size:11px;"><?php echo $operation; ?></td>
                                <td align="center"><?php echo $status; ?></td>
                                <td>
                                    <?php
                                    if($center['AUTH'] == "NOT") {
                                        ?><a class="btn authCReq btn-success btn-xs" crid="<?php echo $center['id']; ?>" req_type="<?php echo $center['req_type']; ?>" data-toggle="tooltip" data-placement="top" title="Authenticate"><i class="fa fa-check"></i></a> <?php
                                        if($center['req_type'] != "CENTER_DELETE") {
                                            if($center['id_old'] == 0) {
                                                ?><a href="<?php echo $this->base_url."/centers/temp_edit/".$center['id']; ?>" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" title="Edit New Center before authentication"><i class="fa fa-edit"></i></a><?php
                                            } else {
                                                ?><a href="<?php echo $this->base_url."/centers/temp_edit/".$center['id']; ?>" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" title="Edit Existing Center Edit Request"><i class="fa fa-edit"></i></a><?php
                                            }
                                        }
                                        ?> <a class="btn delCReq btn-danger btn-xs" crid="<?php echo $center['id']; ?>" data-toggle="tooltip" data-placement="top" title="Delete before authentication"><i class="fa fa-times"></i></a><?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</section>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDvaUg89uMNUQ3CSkUpio6dD0IudZ2ZWmQ"></script>
<script type="text/javascript">
var cntAuthCenId = 0;

function showInfo(field, val1, val2) {
    if(val1 != "") {
        $('.'+field+'O').html(val1);
    } else {
        $('.'+field+'O').html("Not Present");
    }
    if(val2 != "") {
        $('.'+field+'N').html(val2);
    } else {
        $('.'+field+'N').html("Not Present");
    }
    if(val1 == val2) {
        $('.'+field+'O').removeClass("bg-red").addClass("bg-green");
        $('.'+field+'N').removeClass("bg-red").addClass("bg-green");
    } else {
        $('.'+field+'O').removeClass("bg-green").addClass("bg-red");
        $('.'+field+'N').removeClass("bg-green").addClass("bg-red");
    }
}

function convertTime(day, time) {
    var th = time;
    hour = parseInt(th / 100);
    min = parseInt(th - (hour * 100));
    if(min == 0)
        min = "00";
    ampm = "AM";
    if(hour >= 12) {
        ampm = "PM";
        hour = hour - 12;
    }
    return "Every "+day+" "+hour+":"+min+""+ampm;
}

var cAuthMap;
var cLocOld, cLocNew;
var bounds = new google.maps.LatLngBounds();

function initializeMap() {
    if(cLocNew == null || (cLocNew.lat() == 0 && cLocNew.lng() == 0)) {
        $(".cLocation").css("display", "none");
    } else {
        $(".cLocation").css("display", "table-cell");
        var mapOptions = {
            zoom: 8,
            center: new google.maps.LatLng(0,0),
            scrollwheel: false,
            panControl: false,
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            overviewMapControl: false
        };
        cAuthMap = new google.maps.Map(document.getElementById('center_map'), mapOptions);

        console.log(cLocOld);
        console.log(cLocNew);

        if(cLocOld.lat() != cLocNew.lat() && cLocOld.lng() != cLocNew.lng()) {

            var markerOld = new google.maps.Marker({position: cLocOld, map: cAuthMap, title:"Old Location", icon:{path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW, strokeColor: "red", scale: 3}, animation: google.maps.Animation.DROP});
            var markerNew = new google.maps.Marker({position: cLocNew, map: cAuthMap, title:"New Location", icon:{path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW, strokeColor: "blue", scale: 3}, animation: google.maps.Animation.DROP});

            bounds.extend(markerOld.position);
            bounds.extend(markerNew.position);

            var iwOld = new google.maps.InfoWindow({content: "Old Location"});
            var iwNew = new google.maps.InfoWindow({content: "New Location"});

            google.maps.event.addListener(markerOld, 'click', function() { iwOld.open(cAuthMap,markerOld); });
            google.maps.event.addListener(markerNew, 'click', function() { iwNew.open(cAuthMap,markerNew); });

            markerNew.setAnimation(google.maps.Animation.BOUNCE);

            setTimeout(function(){ markerNew.setAnimation(null); }, 3000);

            cAuthMap.fitBounds(bounds);
        } else {
            var markerNew = new google.maps.Marker({position: cLocNew, map: cAuthMap, title:"No Location change", icon:{path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW, strokeColor: "blue", scale: 3}, animation: google.maps.Animation.DROP});

            bounds.extend(markerNew.position);

            var iwNew = new google.maps.InfoWindow({content: "No Location change"});

            google.maps.event.addListener(markerNew, 'click', function() { iwNew.open(cAuthMap,markerNew); });

            markerNew.setAnimation(google.maps.Animation.BOUNCE);

            setTimeout(function(){ markerNew.setAnimation(null); }, 3000);

            cAuthMap.fitBounds(bounds);
        }
    }
}

$(function() {
    $(".delCReq").on("click", function() {
        var crid = $(this).attr("crid");
        var r = confirm("Are you sure about deleting your center request");
        if(r == true) {
            $.getJSON( base_url+"/centers/delCenterReq?crid=" + crid, function( data ) {
                if(data.status == "SUCCESS") {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.reload();
                }
            });
        }
    });

    $(".authCReq").on("click", function() {
        cntAuthCenId = $(this).attr("crid");
        cntReqType = $(this).attr("req_type");
        $.getJSON( base_url+"/ajax/getCenterCompare?crid=" + cntAuthCenId, function( data ) {
            if(data.status == "SUCCESS") {
                if(cntReqType == "CENTER_DELETE") {
                    $(".editCell").css("display", "none");
                    $("#centerRequestType").html("<span class='cl-red'>Center Deletion Request</span>");
                    $("#editCenterLink").css("display", "none");
                } else {
                    $(".editCell").css("display", "table-cell");
                    $("#centerRequestType").html("<span class='cl-red'>"+cntReqType+"</span>");
                    $("#editCenterLink").css("display", "inline-block");
                    $("#editCenterLink").attr("href", base_url + "/centers/temp_edit/"+data.centerN.id);
                }

                console.log(data.centerO.address);
                console.log(data.centerN.address);

                showInfo("cPlace", data.centerO.place, data.centerN.place);
                showInfo("cCountry", data.centerO.country, data.centerN.country);
                showInfo("cState", data.centerO.state, data.centerN.state);
                showInfo("cDistrict", data.centerO.district, data.centerN.district);
                showInfo("cCity", data.centerO.city, data.centerN.city);
                showInfo("cAddress", data.centerO.address, data.centerN.address);
                showInfo("cTimeDay", convertTime(data.centerO.time_day, data.centerO.time_hour), convertTime(data.centerN.time_day, data.centerN.time_hour));

                showInfo("cC1Name", data.centerO.contact_1_name, data.centerN.contact_1_name);
                showInfo("cC1Phone", data.centerO.contact_1_phone, data.centerN.contact_1_phone);
                showInfo("cC1Email", data.centerO.contact_1_email, data.centerN.contact_1_email);
                showInfo("cC2Name", data.centerO.contact_2_name, data.centerN.contact_2_name);
                showInfo("cC2Phone", data.centerO.contact_2_phone, data.centerN.contact_2_phone);
                showInfo("cC2Email", data.centerO.contact_2_email, data.centerN.contact_2_email);
                showInfo("cStrength", data.centerO.strength, data.centerN.strength);

                cLocOld = new google.maps.LatLng(data.centerO.lat, data.centerO.lng);
                cLocNew = new google.maps.LatLng(data.centerN.lat, data.centerN.lng);

                var hist  = JSON.parse(data.centerN.transactions);
                var histContent = "";
                for (var i = hist.length - 1; i >= 0; i--) {
                    h = hist[i];
                    //console.log(h);
                    if(h.req_type == "CENTER_ADD") {
                        histContent += '<i class="fa fa-plus-circle cl-green"></i> New Center Added by User '+h.user_id+' on '+h.time_trans+' via '+h.medium+'<br>';
                    } else if(h.req_type == "CENTER_EDIT_TEMP") {
                        histContent += '<i class="fa fa-pencil-square cl-yellow"></i> Temporary Center Edited by User '+h.user_id+' on '+h.time_trans+' via '+h.medium+'<br>';
                    } else if(h.req_type == "CENTER_EDIT") {
                        histContent += '<i class="fa fa-pencil-square cl-yellow"></i> Center Edited by User '+h.user_id+' on '+h.time_trans+' via '+h.medium+'<br>';
                    } else if(h.req_type == "CENTER_DELETE") {
                        histContent += '<i class="fa fa-times-circle cl-red"></i> Center deletion request by User '+h.user_id+' on '+h.time_trans+' via '+h.medium+'<br>';
                    }
                };
                
                $(".cHistory").html(histContent);
                $('#authCenterReq').modal('show');
            }
        });
    });

    $('#authCenterReq').on('shown.bs.modal', function (e) {
        initializeMap();
    });

    

    $("#authCenterReqFinal").on("click", function() {
        $.getJSON( base_url+"/centers/authCenterReq?crid=" + cntAuthCenId, function( data ) {
            if(data.status == "SUCCESS") {
                window.location.href = data.redirect_url;
            } else {
                window.location.reload();
            }
        });
    });
});
</script>

<div class="modal fade" id="authCenterReq" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Center Request Authentication</h4>
            </div>
            <div class="modal-body">
                <center>
                    <h3 id="centerRequestType"></h3>
                </center>
                <table class="table table-bordered centerCompare">
                    <thead>
                        <tr>
                            <th>Current Information</th>
                            <th class="editCell">Edited Information</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr title="Center Name" data-toggle="tooltip" data-placement="top"><td class="cPlaceO"></td><td class="cPlaceN editCell"></td></tr>
                        <tr title="Center Country" data-toggle="tooltip" data-placement="top"><td class="cCountryO"></td><td class="cCountryN editCell"></td></tr>
                        <tr title="Center State" data-toggle="tooltip" data-placement="top"><td class="cStateO"></td><td class="cStateN editCell"></td></tr>
                        <tr title="Center District" data-toggle="tooltip" data-placement="top"><td class="cDistrictO"></td><td class="cDistrictN editCell"></td></tr>
                        <tr title="Center City" data-toggle="tooltip" data-placement="top"><td class="cCityO"></td><td class="cCityN editCell"></td></tr>
                        <tr title="Center Address" data-toggle="tooltip" data-placement="top"><td class="cAddressO"></td><td class="cAddressN editCell"></td></tr>
                        <tr title="Center Time" data-toggle="tooltip" data-placement="top"><td class="cTimeDayO"></td><td class="cTimeDayN editCell"></td></tr>
                        <tr title="Center Co-ordinator Name" data-toggle="tooltip" data-placement="top"><td class="cC1NameO"></td><td class="cC1NameN editCell"></td></tr>
                        <tr title="Center Co-ordinator Phone" data-toggle="tooltip" data-placement="top"><td class="cC1PhoneO"></td><td class="cC1PhoneN editCell"></td></tr>
                        <tr title="Center Co-ordinator Email" data-toggle="tooltip" data-placement="top"><td class="cC1EmailO"></td><td class="cC1EmailN editCell"></td></tr>
                        <tr title="Center Co-ordinator Name" data-toggle="tooltip" data-placement="top"><td class="cC2NameO"></td><td class="cC2NameN editCell"></td></tr>
                        <tr title="Center Co-ordinator Phone" data-toggle="tooltip" data-placement="top"><td class="cC2PhoneO"></td><td class="cC2PhoneN editCell"></td></tr>
                        <tr title="Center Co-ordinator Email" data-toggle="tooltip" data-placement="top"><td class="cC2EmailO"></td><td class="cC2EmailN editCell"></td></tr>
                        <tr title="Center Strength" data-toggle="tooltip" data-placement="top"><td class="cStrengthO"></td><td class="cStrengthN editCell"></td></tr>
                        <tr title="Center Location" data-toggle="tooltip" data-placement="top"><td class="cLocation" colspan="2"><div id="center_map"></div></td></tr>
                        <tr title="Center History" data-toggle="tooltip" data-placement="top"><td class="cHistory" colspan="2"></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a id="editCenterLink" class="btn btn-primary" href="">Edit</a>
                <button type="button" class="btn btn-success" id="authCenterReqFinal">Authenticate</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo $this->base_url; ?>/js/intro.min.js"></script>
<script type="text/javascript">
    javascript:introJs().start();
</script>

