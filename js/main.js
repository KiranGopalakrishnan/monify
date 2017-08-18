$(document).ready(function(){
  /**
  ***   GLOBAL VARIABLES
  **/
  var BASE_URL="./php/";
  var PAGINATION_THRESHOLD=100;
  //counter for last loaded results-for pagination purposes
  var current_loaded_data_start = 0;
  //Stores the last loaded data for pagination purposes
  var last_loaded_data_count =0;
  //Current data loaded from this script
  var current_data_loaded_script="";
  var currently_showing_results_for="All active users";
  //Setting max date of date of birth field to today
  setDobMaxToToday()
  //Intial loading of all users
  loadAllUsers();

  function loadAllUsers(){
    current_loaded_data_start = 0;
    var script_name = "get_all_users.php";

    setCurrentlyShowing("All Members");

    current_data_loaded_script=BASE_URL+script_name
    showLoading();
    $.get(BASE_URL+script_name,{start:current_loaded_data_start,threshold:PAGINATION_THRESHOLD},function(response){
      var reply = JSON.parse(response);
      var requestStatus = reply["result"];
      if(requestStatus){
        var data = reply["data"];

        var total_num_results = reply["total_num_results"];
        currentRangeDisplay(total_num_results);
        $(".items_container").html("");
        data.forEach(function(singleUser){
          var user_box_html = generateHtml(singleUser.firstname,singleUser.lastname,singleUser.id);
          $(".items_container").append(user_box_html);
        });
      }else {
        var html = generateNoDataFoundHtml("An error occured");
        $(".items_container").html(html);
        $(".pager").hide();
      }
      hideLoading();
    });
  }
  //Loads the next batch of results from current_data_loaded_script variable and displays the result
  $("body").on("click",".next",nextClickHandler);

  function nextClickHandler(){
    var isLast = $(".next").attr("data");
    if(isLast!="last"){
      //Increase the starting point to next extraction point
      current_loaded_data_start += PAGINATION_THRESHOLD;
      //Check if the previous button should be displayed-only of there is data to go back to
      if(current_loaded_data_start>=PAGINATION_THRESHOLD){
        $(".previous").show();
      }
      showLoading();
      ///Adds Pagination threshold to the previous data_end point to arrive at the next end point
      $.get(current_data_loaded_script,{start:current_loaded_data_start,threshold:PAGINATION_THRESHOLD},function(response){
        var reply = JSON.parse(response);
        var requestStatus = reply["result"];  //contains the query execuion status true/false
        if(requestStatus){  //checks if the query was Successful or not

          var data = reply["data"];
          $(".items_container").html(""); ///Clear the item container
          if(data.length<PAGINATION_THRESHOLD){
            $(".next").attr("data","last");
          }
          if(data.length<1){
            var html = generateNoDataFoundHtml();
            $(".items_container").html(html);
            $(".pager").hide();
          }

          var total_num_results = reply["total_num_results"];
          currentRangeDisplay(total_num_results);

          data.forEach(function(singleUser){
            var user_box_html = generateHtml(singleUser.firstname,singleUser.lastname,singleUser.id);
            $(".items_container").append(user_box_html);
          });
        }else {
          var html = generateNoDataFoundHtml("An error occured");
          $(".items_container").html(html);
          $(".pager").hide();
        }
        hideLoading();
      });
    }else{
      $(".next").hide();
    }
  }

  //Loads the previous batch of results from current_data_loaded_script variable and displays the result
  $("body").on("click",".previous",previousClickHandler);
  function previousClickHandler(){
    ///Subtracts Pagination threshold from the previous data_end point to arrive at the next end point
    if((current_loaded_data_start-PAGINATION_THRESHOLD)>=0){
      current_loaded_data_start -= PAGINATION_THRESHOLD;

      if(current_loaded_data_start>=0){
        showLoading();
        //$(".previous").show();
        $.get(current_data_loaded_script,{start:current_loaded_data_start,threshold:PAGINATION_THRESHOLD},function(response){
          var reply = JSON.parse(response);
          var requestStatus = reply["result"];  //contains the query execuion status true/false
          if(requestStatus){  //checks if the query was Successful or not

            var data = reply["data"];
            $(".items_container").html(""); ///Clear the item container
            if(data.length<1){
              var html = generateNoDataFoundHtml();
              $(".items_container").html(html);
              $(".pager").hide();
            }else{
              $(".pager").show();
            }

            var total_num_results = reply["total_num_results"];
            currentRangeDisplay(total_num_results);

            data.forEach(function(singleUser){
              var user_box_html = generateHtml(singleUser.firstname,singleUser.lastname,singleUser.id);
              $(".items_container").append(user_box_html);
            });
          }else {
            var html = generateNoDataFoundHtml("An error occured");
            $(".items_container").html(html);
            $(".pager").hide();
          }
          hideLoading();
        });
      }
    }else{
      $(".previous").hide();
    }
  }

  //Handles view details button click
  $("body").on("click",".btn-view",viewDetailsButtonClickHandler);
  var firstname,lastname,email,address,city,province,dob,postalcode,telephone;
  function viewDetailsButtonClickHandler(e){
    var script_name ="get_single_user.php"
    showLoading();
    var user_id = $(this).closest(".single_user_item").attr("data-id");  //user id is stored as the data-id

    $.get(BASE_URL+script_name,{id:user_id},function(response){

      var reply = JSON.parse(response);
      var requestStatus = reply["result"];
      if(requestStatus){

        var data = reply["data"];
        var userData= data[0];
        $("#userDetailsModal").attr("data-id",userData.id);
        $("#userDetailsModal .modal-title").html(userData.firstname+" "+userData.lastname);
        $("#id .data").html(userData.id);
        $("#firstname .data").html(userData.firstname);
        $("#lastname .data").html(userData.lastname);
        $("#email .data").html(userData.email);
        $("#address .data").html(userData.address);
        $("#city .data").html(userData.city);
        $("#dob .data").html(userData.dob);
        $("#postalcode .data").html(userData.postalcode);
        $("#province .data").html(userData.province);
        $("#telephone .data").html(userData.telephone);
        firstname = userData.firstname;
        lastname = userData.lastname;
        email=userData.email;
        address=userData.address;
        city=userData.city;
        dob=userData.dob;
        province=userData.province;
        telephone=userData.telephone;
        postalcode=userData.postalcode;
        var status = userData.status==1?"Active":"Deleted";
        if(userData.status==0){
          $("#userDetailsModal .btn-delete").attr("disabled","true");
        }else{
          $("#userDetailsModal .btn-delete").removeAttr("disabled");
        }
        $("#status .data").html("<span class='active'>"+status+"</span>");

        hideLoading();
        $("#userDetailsModal").modal("show");
      }
    });
  }
  //Handles delete button click
  $("body").on("click","#userDetailsModal .btn-delete",deleteButtonClickHandler);

  function deleteButtonClickHandler(){
    showLoading();
    var script_name ="delete_user.php"
    var user_id = $("#userDetailsModal").attr("data-id");
    var currentElement = $(this);
    $.get(BASE_URL+script_name,{id:user_id},function(response){
      var data = JSON.parse(response);
      $("#userDetailsModal").modal("hide");
      if(data.result){

        showResultModal("Deleted Successfully");
        $(".items_container").find('.single_user_item[data-id="'+user_id+'"]').hide();
        currentElement.attr("disabled","true");
      }else{
        showResultModal("An error occured while deleting");
      }
      hideLoading();
    });
  }

  //Handles the click on Edit button
  $("body").on("click","#userDetailsModal .btn-edit",editButtonClickHandler);

  function editButtonClickHandler(){
    var userId = $("#userDetailsModal").attr("data-id");
    $("#user_management_form input#id").val(userId);
    $("#add_new_member_modal .id-box").show();
    $("#user_management_form").attr("data","update");
    $("#userDetailsModal").modal("hide");
    $("#add_new_member_modal").modal("show");
    $("#add_new_member_modal input#firstname").val(firstname);
    $("#add_new_member_modal input#lastname").val(lastname);
    $("#add_new_member_modal input#email").val(email);
    $("#add_new_member_modal input#dob").val(dob);
    $("#add_new_member_modal input#address").val(address);
    $("#add_new_member_modal input#city").val(city);
    $("#add_new_member_modal select#province").val(province);
    $("#add_new_member_modal input#postalcode").val(postalcode);
    $("#add_new_member_modal input#telephone").val(telephone);
  }
  //Event for handling the click on add new member button
  $("body").on("click",".add_new_member",addNewMemberButtonClickHandler);

  function addNewMemberButtonClickHandler(){
    clearForm();
    $("#user_management_form").attr("data","new");
    $("#add_new_member_modal .modal-title").html("Add New User");
    $("#add_new_member_modal .id-box").hide();
    $("#add_new_member_modal").modal("show");
  }
  //Mananages update/add_new user form submission
  $("body").on("submit","#user_management_form",userManagementFormSubmitHandler);
  function userManagementFormSubmitHandler(e){
    e.preventDefault();
    var validated = validateUserForm();
    if(validated){
      showLoading();
      var data = $("form#user_management_form").serialize();  //Serialize the form data for the request
      var request_type = $(this).attr("data");  // Contains request type => new/update
      var script_name = getScriptNameForSubmission(request_type)

      $.post(BASE_URL+script_name,data,function(response){
        var reply = JSON.parse(response);
        var requestStatus = reply["result"];

        clearForm();
        $("#add_new_member_modal").modal("hide");
        if(requestStatus){
          showResultModal("Updated Successfully");
        }else{
          showResultModal("An error occured while updating</br>This E-mail may already be linked to an account !");
        }
        hideLoading();
      });
    }
  }
  //Returns the script to be used for submission
  //based on request type {either New user / Update user}
  function getScriptNameForSubmission(request_type){
    return request_type=="new"?"add_new_user.php":"edit_user.php";
  }

  function showResultModal(message){
    $("#displayResult").modal("show");
    $("#displayResult .modal-content .modal-body").html("<h3>"+message+"</h3>");
  }
  //Handles view all users click
  $("body").on("click",".dropdown-menu li a.viewAll",loadAllUsers);

  //Handles view deleted users click
  $("body").on("click",".dropdown-menu li a.viewDeleted",viewDeletedButtonClickHandler);
  function viewDeletedButtonClickHandler(){
    resetViewDetailModal();
    showLoading();
    var script_name = "view_deleted_users.php";

    setCurrentlyShowing("Deleted members");
    current_data_loaded_script = BASE_URL+script_name;
    current_loaded_data_start = 0;
    $.get(BASE_URL+script_name,{start:current_loaded_data_start,threshold:PAGINATION_THRESHOLD},function(response){
      var reply = JSON.parse(response);
      var requestStatus = reply["result"];
      if(requestStatus){
        var data = reply["data"];
        $(".items_container").html("");
        if(data.length<1){
          var html = generateNoDataFoundHtml();
          $(".items_container").html(html);
          $(".pager").hide();
        }else{
          if(data.length<PAGINATION_THRESHOLD){
            $(".pager").hide();
          }else{
            $(".pager").show();
          }
        }

        var total_num_results = reply["total_num_results"];
        currentRangeDisplay(total_num_results);

        data.forEach(function(singleUser){
          var user_box_html = generateHtml(singleUser.firstname,singleUser.lastname,singleUser.id);
          $(".items_container").append(user_box_html);
        });
      }else {
        var html = generateNoDataFoundHtml("An error occured");
        $(".items_container").html(html);
        $(".pager").hide();
      }
      hideLoading();
    });
  }
  //Handles sort by province
  $("#provinceList").on("change",provinceChangeHandler);
  function provinceChangeHandler(e){
    resetViewDetailModal();
    var selected_province = $(this).val();
    setCurrentlyShowing("Sort By Province::"+selected_province);
    if(selected_province!="ALL"){
      showLoading();
      var script_name = "sort_by_province.php";
      current_data_loaded_script = BASE_URL+script_name+"?province="+selected_province;  //This will be passed on as URL when user clicks on Next button
      current_loaded_data_start = 0;
      $.get(BASE_URL+script_name,{province:selected_province,start:current_loaded_data_start,threshold:PAGINATION_THRESHOLD},function(response){
        var reply = JSON.parse(response);
        var requestStatus = reply["result"];
        if(requestStatus){
          var data = reply["data"];
          $(".items_container").html("");
          if(data.length<1){
            var html = generateNoDataFoundHtml();
            $(".items_container").html(html);
            $(".pager").hide();
          }else{
            $(".pager").show();
          }
          var total_num_results = reply["total_num_results"];
          currentRangeDisplay(total_num_results);
          data.forEach(function(singleUser){
            var user_box_html = generateHtml(singleUser.firstname,singleUser.lastname,singleUser.id);
            $(".items_container").append(user_box_html);
          });
        }else {
          var html = generateNoDataFoundHtml("An error occured");
          $(".items_container").html(html);
          $(".pager").hide();
        }
        hideLoading();
      });
    }else{
      loadAllUsers();
    }

  }
  /**
  * Validation functions
  **/
  function validateUserForm(){
    var firstname = $.trim($("input#firstname").val());
    var lastname = $.trim($("input#lastname").val());
    var email = $.trim($("input#email").val());
    var dob = $.trim($("input#dob").val());
    var address = $.trim($("input#address").val());
    var city = $.trim($("input#city").val());
    var province = $.trim($("select#province").val());
    var postalcode = $.trim($("input#postalcode").val());
    var telephone = $.trim($("input#telephone").val());
    var response = true;
    hideAllErrors();
    if(firstname.length>30||firstname.length<1){
      showError($("input#firstname"));
      response =false;
    }else if(lastname.length>30||lastname.length<1){
      showError($("input#lastname"));
      response =false;
    }else if(email.length>50||email.length<1){
      showError($("input#email"));
      response =false;
    }
    else if(address.length>100||address.length<1){
      showError($("input#address"));
      response =false;
    }
    else if(city.length>50||city.length<1){
      showError($("input#city"));
      response =false;
    }
    else if(province.length>2||province.length<1||province=="NULL"){
      showError($("select#province"));
      response =false;
    }else if(!dob){
      showError($("input#dob"));
      response =false;
    }else if(postalcode.length>7){
      showError($("input#postalcode"));
      response =false;
    }else if(telephone.length>12){
      showError($("input#telephone"));
      response =false;
    }
    return response;
  }
  function showError(element){
    element.closest(".form-group").addClass("has-error");
    element.next(".help-block").addClass("show");
  }
  function hideAllErrors(){
    $("#user_management_form .form-group").removeClass("has-error");
    $(".help-block").removeClass("show");
  }
  function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }
  function validatePhone(inputtxt) {
    var phoneno = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
    if(inputtxt.value.match(phoneno)) {
      return true;
    }
    else {
      return false;
    }
  }
  //Clears the form
  function clearForm(){
    $("#user_management_form").find("input[type=text], select,input[type=date],input[type=email]").val("");
    $("#user_management_form").find("input[type=text], select,input[type=date],input[type=email]").blur();
  }
  //genrates the html and populates it
  function generateHtml(firstname,lastname,id){
    var user_box_html = "<div class=\"card single_user_item\" data-id="+id+">";
    user_box_html += "<div class=\"card-body\">";
    user_box_html +=  "<div class=\"card_title_container\">";
    user_box_html +=  "<h4 class=\"card-title\">"+firstname+" "+lastname+"</h4>";
    user_box_html +=  "<h5 class=\"card-title sub_title\">Member ID : "+id+"</h5>";
    user_box_html +="</div>";
    user_box_html +="<div class=\"btn_container\">  <a class=\"btn btn-view btn-primary\">View Details</a> </div>";
    user_box_html +="</div>";
    user_box_html +="</div>";
    return user_box_html;
  }
  function generateNoDataFoundHtml(message ="No data found for your request"){
    return "<div class='no_data_found'>"+message+"</div>";
  }
  function resetViewDetailModal(){
    $("#userDetailsModal .btn-edit").removeAttr("disabled");
  }
  function showLoading(){
    $(".overlay").stop().css("display","flex").fadeIn(500);
  }
  function hideLoading(){
    $(".overlay").stop().fadeOut(500);
  }
  function setCurrentlyShowing(currently_showing){
    $(".currently_showing h3").html(currently_showing)
  }
  function setDobMaxToToday(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    if(dd<10){
      dd='0'+dd
    }
    if(mm<10){
      mm='0'+mm
    }

    today = yyyy+'-'+mm+'-'+dd;
    $("#user_management_form input#dob").attr("max", today);
  }
  function currentRangeDisplay(total_num_results){
    if(total_num_results>0){
      $(".number_of_results").show();
    var current_end = (current_loaded_data_start+PAGINATION_THRESHOLD);
    var current_range_end = total_num_results<current_end?total_num_results:current_end;
    $(".total_num_results").html(total_num_results);
    $(".current_result_range").html(current_loaded_data_start+" - "+current_range_end);
  }else{
    $(".number_of_results").hide();
  }
  }
});
