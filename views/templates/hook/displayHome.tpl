<div class="tribu-module-faq">
    {if isset($faqs) && !empty($faqs)}
        <h2>{l s='FAQ' mod='tribufaq'}</h2>
        {foreach $faqs as $faq properties=[iteration]}
            <div class="faq__category accordion">
                <button class="faq__category-name accordion__title" type="button" aria-expanded="false" id="faq-category-{$faq@iteration}">
                    {$faq.category_name}
                    <svg viewBox="0 0 22 32" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2"><path d="m1.301 28.104 12.177-12.281L1.197 3.646 4.951-.14l16.068 15.931L5.087 31.859l-3.786-3.755Z" style="fill:currentColor;fill-rule:nonzero"/></svg>
                </button>
                <section class="faq__category-accordions accordion__content" aria-labelledby="faq-category-{$faq@iteration}">
                    {foreach $faq.questions as $question properties=[iteration]}
                        <div class="faq__accordion accordion">
                            <button class="faq__accordion-title accordion__title" type="button" aria-expanded="false" id="accordion-{$faq@iteration}-{$question@iteration}">
                                {$question.question}
                                <svg viewBox="0 0 22 32" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2"><path d="m1.301 28.104 12.177-12.281L1.197 3.646 4.951-.14l16.068 15.931L5.087 31.859l-3.786-3.755Z" style="fill:currentColor;fill-rule:nonzero"/></svg>
                            </button>
                            <section class="faq__accordion-content accordion__content" aria-labelledby="accordion-{$faq@iteration}-{$question@iteration}">
                                {$question.response nofilter}
                            </section>
                        </div>
                    {/foreach}
                </section>
            </div>
        {/foreach}
    {/if}
</div>
