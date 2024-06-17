// console.log('Tribu Module test');

(function ($) {

    $('.faq__category-name').click(function() {
        var $this = $(this);
        var $category = $this.parent('.faq__category');
        var $openedCategories = $('.faq__category.active');
        var $openedCategoriesTitles = $('.faq__category.active .faq__category-name');

        if($category.length) {
            $('.faq__category-accordions').removeClass('active');

            if($openedCategories.length) {
                $openedCategories.each(function(index, box) {
                    if(box === $category[0]) {
                        $openedCategories.splice(index, 1);
                    }
                });
                if($openedCategories.length) {
                    $openedCategories.removeClass('active');
                    $openedCategoriesTitles.attr('aria-expanded', function (i, attr) {
                        return attr == 'false'
                    });
                }
            }
            $category.toggleClass('active');
            $this.attr('aria-expanded', function (i, attr) {
                return attr == 'true' ? 'false' : 'true'
            });
        }
    });

    $('.faq__accordion-title').click(function() {
        var $this = $(this);
        var $accordion = $this.parent('.faq__accordion');
        var $openedAccordions = $('.faq__accordion.active');
        var $openedAccordionsTitles = $('.faq__accordion.active .faq__accordion-title');

        if($accordion.length) {
            $('.faq__accordion-content').removeClass('active');

            if($openedAccordions.length) {
                $openedAccordions.each(function(index, box) {
                    if(box === $accordion[0]) {
                        $openedAccordions.splice(index, 1);
                    }
                });
                if($openedAccordions.length) {
                    $openedAccordions.removeClass('active');
                    $openedAccordionsTitles.attr('aria-expanded', function (i, attr) {
                        return attr == 'false'
                    });
                }
            }
            $accordion.toggleClass('active');
            $this.attr('aria-expanded', function (i, attr) {
                return attr == 'true' ? 'false' : 'true'
            });
        }
    });
})(jQuery);
