<% include SlideShowBlockSlideIntro %>
<% if $Items %>
<div class="slideshow-pricing__factors">
    <% loop $Items %>
    <div class="slideshow-pricing__factor">
        <div class="slideshow-pricing__label"><h4>{$Pos}. $Title</h4><% if $Text %><p class="no-top-margin small">$Text</p><% end_if %></div>
        <div>
            <div class="slideshow-pricing__track" style="--v: {$ValuePercent}%" aria-hidden="true">
                <span class="slideshow-pricing__fill"></span><span class="slideshow-pricing__thumb"></span>
            </div>
            <div class="slideshow-pricing__scale" aria-hidden="true"><small>$Label</small><small>$LabelAlt</small></div>
        </div>
    </div>
    <% end_loop %>
</div>
<% end_if %>
<% if $ExtraItems %>
<div class="slideshow-pricing__bands">
    <% loop $ExtraItems %>
    <div class="slideshow-pricing__band<% if $Highlight %> slideshow-pricing__band--highlight<% end_if %>">
        <h4>$Title</h4>
        <p><span class="slideshow-pricing__price">$Label</span><% if $Text %>, <span class="slideshow-pricing__note">$Text</span><% end_if %></p>
    </div>
    <% end_loop %>
</div>
<% end_if %>
<% include SlideShowBlockSlideOutro %>
