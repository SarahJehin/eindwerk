(function ( window, document, $, undefined ) {

    //
    angular.module("dashboard_sportiva").controller("ExerciseController", function ($scope, $http) {

        $scope.filtered = false;

        $scope.handle_filter = function($event, page) {
            var checked_tag_ids = [];
            //get all checked checkboxes (this doesn't get the currently clicked checkbox)
            var checked_tags = $('.filters_block .tag input:checked');
            $.each(checked_tags, function(key, value){
                var tag_id = $(value).attr('id');
                checked_tag_ids.push(tag_id);
            });
            //get currently clicked input
            if($event) {
                var currently_clicked_input = $($event.currentTarget).parent().find('input')[0];
                var current_tag_id = $(currently_clicked_input).attr('id');
                //if the checked property is false, it actually means it was false and so is true now
                if(!currently_clicked_input.checked) {
                    //add to ids array
                    checked_tag_ids.push(current_tag_id);
                }
                else {
                    //remove from array
                    var index = checked_tag_ids.indexOf(current_tag_id);
                    if (index >= 0) {
                      checked_tag_ids.splice( index, 1 );
                    }
                    if(checked_tag_ids.length == 0) {
                        $('.newest').show();
                        $('.most_viewed').show();
                        $('.all').show();
                        $scope.filtered = false;
                    }
                }
            }
            
            console.log(checked_tag_ids);
            //if not empty
            
            if(checked_tag_ids.length > 0) {
                //get request
                //hide newest and most viewed
                $http({
                    method: 'GET',
                    url: location.origin + '/get_filtered_exercises',
                    params: {tag_ids: JSON.stringify(checked_tag_ids), page: page}
                }).then(function successCallback(response) {
                    console.log(response);
                    $scope.filtered_exercises = response.data.filtered_exercises.data;
                    console.log($scope.filtered_exercises);
                    var pagination_string = response.data.pagination_html;
                    pagination_string = pagination_string.split('<a').join('<div').split('</a>').join('</div>');
                    console.log(pagination_string);
                    $('.pagination_container').html(pagination_string);
                    $('.pagination_container div').removeAttr('href');
                    //$('.pagination_links a').attr('href', '#');
                    //$('.pagination_links div').attr('ng-click', 'handle_filter($event, 2)');
                    $('.newest').hide();
                    $('.most_viewed').hide();
                    $('.all').hide();
                    $scope.filtered = true;

                    //console.log($('.exercises_content').children());
                    setTimeout(function() {
                        //console.log($('.exercises_content').children());
                        var images = $('.filtered_exercises .image');
                        //console.log(images);
                        $.each(images, function(key, value) {
                            var image_width = parseInt($(value).css('width'));
                            var image_height = image_width/4*3;
                            $(value).css('height', image_height + 'px');
                        });
                    }, 10);
                    

                }, function errorCallback(response) {
                    console.log(response);
                });
            }
            else {
                //show newest and most viewed
            }
            
        }

        $('.exercises_overview .block').on('click', '.pagination_container .pagination li div', function() {
            console.log('tezfqsvkjhcvqsdkfj');
            console.log($(this).text());
            var page_nr = $(this).text();
            $scope.handle_filter(null, page_nr);
        });


        //make sure newest image is in 4:3 format
        var image_width = parseInt($('.newest .image').css('width'));
        var image_height = image_width/4*3;
        $('.newest .image').css('height', image_height + 'px');

        //images in 4:3 format
        var images = $('.image');
        $.each(images, function(key, value) {
            var image_width = parseInt($(value).css('width'));
            var image_height = image_width/4*3;
            $(value).css('height', image_height + 'px');
        });

    
});
})(window, window.document, window.jQuery);