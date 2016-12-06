function initImages(imageId){

   if(document.images){

    //define, initialise and pre-cache the down (active) state of off images 
    var imgOffArray = [];
    imgOffArray["b_" + imageId] = new Image(30, 25);
    imgOffArray["i_" + imageId] = new Image(30, 25);
    imgOffArray["u_" + imageId] = new Image(30, 25);

    imgOffArray["b_" + imageId].src = textEditImgPath + "bold_off.png";
    imgOffArray["i_" + imageId].src =  textEditImgPath + "italic_off.png";
    imgOffArray["u_" + imageId].src = textEditImgPath + "underline_off.png";

    // define, initialise and pre-cache the hover state of off images 
    var imgOffHoverArray = [];
    imgOffHoverArray["b_" + imageId] = new Image(30, 25);
    imgOffHoverArray["i_" + imageId] = new Image(30, 25);
    imgOffHoverArray["u_" + imageId] = new Image(30, 25);

    imgOffHoverArray["b_" + imageId].src = textEditImgPath + "bold_off_hover.png";
    imgOffHoverArray["i_" + imageId].src =  textEditImgPath + "italic_off_hover.png";
    imgOffHoverArray["u_" + imageId].src = textEditImgPath + "underline_off_hover.png";

    //define, initialise and pre-cache the down (active) state of on images 
    var imgOnArray = [];
    imgOnArray["b_" + imageId] = new Image(30, 25);
    imgOnArray["i_" + imageId] = new Image(30, 25);
    imgOnArray["u_" + imageId] = new Image(30, 25);

    imgOnArray["b_" + imageId].src = textEditImgPath + "bold_on.png";
    imgOnArray["i_" + imageId].src =  textEditImgPath + "italic_on.png";
    imgOnArray["u_" + imageId].src = textEditImgPath + "underline_on.png";

    //define, initialise and pre-cache the hover state of on images 
    var imgOnHoverArray = [];
    imgOnHoverArray["b_" + imageId] = new Image(30, 25);
    imgOnHoverArray["i_" + imageId] = new Image(30, 25);
    imgOnHoverArray["u_" + imageId] = new Image(30, 25);

    imgOnHoverArray["b_" + imageId].src = textEditImgPath + "bold_on_hover.png";
    imgOnHoverArray["i_" + imageId].src = textEditImgPath + "italic_on_hover.png";
    imgOnHoverArray["u_" + imageId].src = textEditImgPath + "underline_on_hover.png";

   }

   //define functions for the mouse over and mouse out and click states of the image
   imgMouseOver = function(imgName){

      if(document.images[imgName].src == imgOffArray[imgName].src){
       document.images[imgName].src = imgOffHoverArray[imgName].src;
      }
      else if(document.images[imgName].src == imgOnArray[imgName].src){
       document.images[imgName].src = imgOnHoverArray[imgName].src;
      }
   }

   imgMouseOut = function(imgName){
   
      if(document.images[imgName].src == imgOffHoverArray[imgName].src){
       document.images[imgName].src = imgOffArray[imgName].src;
      }   
      else if(document.images[imgName].src == imgOnHoverArray[imgName].src){
       document.images[imgName].src = imgOnArray[imgName].src; 
      }
   }

   imgClick = function(imgName){
   
      if(document.images[imgName].src == imgOffHoverArray[imgName].src ||
       document.images[imgName].src == imgOffArray[imgName].src){
       document.images[imgName].src = imgOnArray[imgName].src;
      }
      else if(document.images[imgName].src == imgOnHoverArray[imgName].src ||
       document.images[imgName].src == imgOnArray[imgName].src){
       document.images[imgName].src = imgOffArray[imgName].src;
      }
   }
}