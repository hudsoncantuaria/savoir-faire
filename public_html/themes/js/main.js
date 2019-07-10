$(document).ready(function () {
    var inputFile01 = $('.input-file'),
            inputFile02 = $('.input-file2'),
            inputFile03 = $('.input-file3');

    function fileInput(inputClass) {
        inputClass.each(function () {
            var $input = $(this),
                    $label = $input.next('.js-labelFile'),
                    labelVal = $label.html();

            $input.on('change', function (element) {
                var fileName = '';
                if (element.target.value)
                    fileName = element.target.value.split('\\').pop();
                fileName ? $label.addClass('has-file').find('.js-fileName').html(fileName) : $label.removeClass('has-file').html(labelVal);
            });
        });
    }
    fileInput(inputFile01);
    fileInput(inputFile02);
    fileInput(inputFile03);

    /* mobile menu trigger */
    $('.burger-icon').click(function () {
        $('.menu-mobile--wrapper').addClass('open');
    });
    $('.close-menu--icon').click(function () {
        $('.menu-mobile--wrapper').removeClass('open');
    });

    /* desktop menu trigger */
    $('.user-login--wrapper').click(function () {
        $('.user-login--list').toggleClass('hide');
    });

    $(".coming-soon").click(function() {
        $(".coming-soon--mask").fadeIn();
    });

    $(".close-coming--btn").click(function() {
        $(".coming-soon--mask").fadeOut();
    });

    
});

