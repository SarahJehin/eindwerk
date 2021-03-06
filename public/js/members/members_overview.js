(function ( window, document, $, undefined ) {

angular.module("dashboard_sportiva").controller("RolesController", function ($scope, $http) {
    //delete project
    $scope.open_roles_modal = function ($event, member_id, member_name) {
        console.log('test');
        //$scope.project_id = project_id;
        //$scope.project_name = project_name;
        $scope.member_id = member_id;
        $scope.member_name = member_name;
        console.log(member_name);
        //get all roles that the authenticated user may update
        $http.get(location.origin + '/get_allowed_update_roles',
            {})
            .success(function(response) {
                $scope.allowed_update_roles = response;
            })
            .error(function(response) {
            });

        //get all already assigned roles for this member
        $http({
            method: 'GET',
            url: location.origin + '/get_user_roles',
            params: {member_id: member_id}
        }).then(function successCallback(response) {
            $scope.assigned_roles = response.data;
        }, function errorCallback(response) {
        });
        
    }

    $scope.update_user_role = function($event, role_id) {
        var new_value = !$($event.currentTarget).parent().find('input')[0].checked;
        $http.post(location.origin + '/update_user_role',
            {
                member_id: $scope.member_id,
                role_id: role_id,
                new_value: new_value
            })
            .success(function(response) {
            })
            .error(function(response) {
            });

    }

    $scope.check_if_role_is_assigned = function(role_id) {
        var checked = $scope.assigned_roles.indexOf(role_id);
        if(checked >= 0) {
            return true;
        }
        else {
            return false;
        }
    }
});

    $('.member_block .vtv_nr').click(open_member_details);
    $('.member_block .name').click(open_member_details);

    function open_member_details() {
        var member_block = $(this).parent().parent();
        member_block.find('.details').slideToggle( 250);
        if(member_block.hasClass('opened')) {
            member_block.removeClass('opened');
        }
        else {
            member_block.addClass('opened');
        }
    }

    $('.open_advanced ').click(function() {
        $('.search .advanced').slideToggle( 250);
    });

    $('.searchbutton').click(function() {
        $('#search_members').submit();
    });
	
    $('.selectpicker').selectpicker();

    $('.bootstrap-select').click(function() {
        $(this).find('.dropdown-menu.open').toggle();
        $(this).removeClass('dropup');
    });


    $(window).click(function() {
        $('.bootstrap-select .dropdown-menu.open').hide();
    });

    $('.import_members').click(function(){
        $('#import_members_modal').fadeIn(350, function() {
        });
    });
    if(errors) {
        $('#import_members_modal').fadeIn(350, function() {
        });
    }

    $('#import_members').change(function() {
        var filename = $(this).val();
        var lastIndex = filename.lastIndexOf("\\");
        if (lastIndex >= 0) {
            filename = filename.substring(lastIndex + 1);
        }
        $('#import_members_modal label').html('<i class="fa fa-file-excel-o" aria-hidden="true"></i> ' + filename);
    });

    $('.details .roles').click(function(){
        $('#roles_modal').fadeIn(350, function() {
        });
    });

})(window, window.document, window.jQuery);