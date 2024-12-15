var toggle = document.getElementById("toggle");
var state = 0;
var section = document.getElementById("section");
section.style.maxHeight = section.scrollHeight + "px";

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