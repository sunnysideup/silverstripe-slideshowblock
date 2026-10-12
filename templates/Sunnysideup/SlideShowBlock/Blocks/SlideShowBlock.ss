<section class="slideshow" id="slideshow-$ID" data-slideshow aria-label="$Title.ATT">
    <header class="slideshow__header">
        <% if $ShowTitle %><h2 class="slideshow__title">$Title</h2><% end_if %>
        <span class="slideshow__counter" aria-live="polite" data-counter></span>
    </header>
    <div class="slideshow__progress" aria-hidden="true"><span class="slideshow__progress-bar" data-progress></span></div>

    <ol class="slideshow__track" data-track>
        <% loop $SlidesForTemplate %>
            <li class="slideshow-slide slideshow-slide--$LayoutKey" data-layout="$LayoutKey" id="slideshow-{$Up.ID}-slide-$Pos">
                $SlideHTML
            </li>
        <% end_loop %>
    </ol>

    <div class="slideshow__controls">
        <ol class="slideshow__dots" data-dots aria-label="<%t SlideShowBlock.ChooseSlide 'Choose slide' %>"></ol>
        <div class="slideshow__nav">
            <button type="button" class="slideshow__btn" data-prev aria-label="<%t SlideShowBlock.Previous 'Previous slide' %>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button type="button" class="slideshow__btn" data-next aria-label="<%t SlideShowBlock.Next 'Next slide' %>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
            </button>
        </div>
    </div>
</section>
