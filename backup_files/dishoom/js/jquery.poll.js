$(function(){
        var loader=$('#poll-loader');
        var pollcontainer=$('#poll-container');
        loader.fadeIn();
        //Load the poll form
        $.get('/ajax/poll.php', '', function(data, status){
                pollcontainer.html(data);
                animateResults(pollcontainer);
                pollcontainer.find('#viewresult').click(function(){
                        //if user wants to see result
                        loader.fadeIn();
                        $.get('ajax/poll.php', 'result=1', function(data,status){
                                pollcontainer.fadeOut(1000, function(){
                                        $(this).html(data);
                                        animateResults(this);
                                    });
                                loader.fadeOut();
                            });
                        //prevent default behavior
                        return false;
                    }).end()
                    .find('#pollform').submit(function(){
                            var selected_val=$(this).find('input[name=poll]:checked').val();
                            if (selected_val != '') {
                                //post data only if a value is selected
                                loader.fadeIn();
                                $.post('ajax/poll.php', $(this).serialize(), function(data, status){
                                        $('#formcontainer').fadeOut(100, function(){
                                                $(this).html(data);
                                                animateResults(this);
                                                loader.fadeOut();
                                            });
                                    });
                            }
                            //prevent form default behavior
                            return false;
                        });
                loader.fadeOut();
            });

        function animateResults(data){
            $("#poll-results div").each(function(){
                    var percentage = $(this).next().text();
                    $(this).css({width: "0%"}).animate({
                            width: percentage}, 'slow');
                });
            $(data).find('.bar').hide().end().fadeIn('slow', function(){
                });
        }
    });
