var toggle = document.getElementById("toggle");
var filterTitle = document.getElementById("filterTitle");
var section = document.getElementById("section");
var state = section.getAttribute("data-state");
if(state == 0)
{
    section.style.maxHeight = section.scrollHeight + "px";
}
else
{
    section.style.maxHeight = 0;
    toggle.classList.add("rotate");
}

toggle.addEventListener("click", function() 
{
    if (state == 0)
    {
        toggle.classList.remove("rotate");
        toggle.classList.remove("deRotate");
        toggle.classList.add("rotate");

        section.style.maxHeight = 0;
        
        state = 1;
    } 
    else
    {
        toggle.classList.add("deRotate");

        section.style.maxHeight = section.scrollHeight + "px";

        state = 0;
    }
});

filterTitle.addEventListener("click", function()
{
    if (state == 0)
    {
        toggle.classList.remove("rotate");
        toggle.classList.remove("deRotate");
        toggle.classList.add("rotate");

        section.style.maxHeight = 0;
        
        state = 1;
    } 
    else
    {
        toggle.classList.add("deRotate");

        section.style.maxHeight = section.scrollHeight + "px";

        state = 0;
    }
});