var imgContainers = document.getElementsByClassName("imgContainer");

for (var i = 0; i < imgContainers.length; i++) {
    console.log("found img-container");
    imgContainers[i]["imageID"] = imgContainers[i].getAttribute("data-imageID");
    imgContainers[i]["deleteButton"] = imgContainers[i].querySelector(".deleteImage");
    imgContainers[i]["input"] = imgContainers[i].querySelector(".deleteInput");
    imgContainers[i]["state"] = 0;

    // Create a closure to maintain the correct context
    (function(container) {
        container["deleteButton"].addEventListener("click", function() {
            console.log("CLICKED!");
            if (container["state"] == 0) {
                container["deleteButton"].style.color = "#ffcccb";
                container["deleteButton"].style.backgroundColor = "red";
                container["input"].value = container["imageID"];

                container["state"] = 1;
            } else {
                container["deleteButton"].style.color = "black";
                container["deleteButton"].style.backgroundColor = "lightgrey";
                container["input"].value = "";

                container["state"] = 0;
            }
        });
    })(imgContainers[i]);
}