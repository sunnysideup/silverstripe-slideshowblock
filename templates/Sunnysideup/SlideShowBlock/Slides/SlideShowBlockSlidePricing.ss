<% include SlideShowBlockSlideIntro %>
<% if $Items %>
<div class="slideshow-pricing__factors">
    <% loop $Items %>
    <div class="slideshow-pricing__factor">
        <div class="slideshow-pricing__label"><b>{$Pos}. $Title</b><% if $Text %><span>$Text</span><% end_if %></div>
        <div>
            <div class="slideshow-pricing__track" style="--v: {$ValuePercent}%" aria-hidden="true">
                <span class="slideshow-pricing__fill"></span><span class="slideshow-pricing__thumb"></span>
            </div>
            <div class="slideshow-pricing__scale" aria-hidden="true"><span>$Label</span><span>$LabelAlt</span></div>
        </div>
    </div>
    <% end_loop %>
</div>
<% end_if %>
<% if $ExtraItems %>
<div class="slideshow-pricing__bands">
    <% loop $ExtraItems %>
    <div class="slideshow-pricing__band<% if $Highlight %> slideshow-pricing__band--highlight<% end_if %>">
        <b>$Title</b>
        <span class="slideshow-pricing__price">$Label</span>
        <% if $Text %><span class="slideshow-pricing__note">$Text</span><% end_if %>
    </div>
    <% end_loop %>
</div>
<% end_if %>
<% include SlideShowBlockSlideOutro %>
