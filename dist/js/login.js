((Drupal, drupalSettings, once) => {
  Drupal.behaviors.ginAccent = {
    attach: function(context) {
      once("ginLogin", ".user-form-page__wallpaper", context).forEach((() => {
        Drupal.ginLogin.randomWallpaper();
      }));
    }
  }, Drupal.ginLogin = {
    randomWallpaper: () => {
      const path = drupalSettings.gin_login.path + "/images/wallpapers/", wallpapers = [ "eberhard-grossgasteiger-f4OFym_1JsM-unsplash.jpg", "eberhard-grossgasteiger-v5qZ8VnPamg-unsplash.jpg", "eberhard-grossgasteiger-KVKYuYjKPnk-unsplash.jpg", "eberhard-grossgasteiger-7JzgB1NpSW4-unsplash.jpg", "claudio-schwarz-XmXQ5Ek60pk-unsplash.jpg", "claudio-schwarz-mYY3NNP47pU-unsplash.jpg", "claudio-schwarz-WuhakAl5JyY-unsplash.jpg", "vlado-paunovic-gZegJ2zI0_I-unsplash.jpg", "micha-sager-lsDJyu_uJhw-unsplash.jpg" ], wallpaper = wallpapers[Math.floor(Math.random() * wallpapers.length)];
      let image = new Image;
      image.src = path + wallpaper, document.querySelector(".user-form-page__wallpaper").appendChild(image);
    }
  };
})(Drupal, drupalSettings, once);