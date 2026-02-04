Installer npm sur macos
# Download and install nvm:
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash

# in lieu of restarting the shell
\. "$HOME/.nvm/nvm.sh"

# Download and install Node.js:
nvm install 24

# Verify the Node.js version:
node -v # Should print "v24.13.0".

# Verify npm version:
npm -v # Should print "11.6.2".

2.1 Initialiser npm et installer Tailwind

cd wp-content/themes/celya-tailwind
npm init -y
# Installer avec le flag legacy
npm install -D tailwindcss@3.4.1 postcss@8.4.32 autoprefixer@10.4.16 --legacy-peer-deps
# Puis les plugins
npm install -D @tailwindcss/forms@0.5.7 @tailwindcss/typography@0.5.10 @tailwindcss/aspect-ratio@0.4.2 --legacy-peer-deps

npx tailwindcss init -p 
OU
npx tailwindcss -i ./assets/css/input.css -o ./assets/css/output.css

--------------------

wp-content/themes/celya/
│
├── assets/
│   ├── css/
│   │   ├── input.css          # CSS source Tailwind
│   │   └── output.css         # CSS compilé (généré)
│   ├── js/
│   │   └── app.js
│   └── images/
│   │   ├── banners/
│   │   └── logo/
│   │   └── pictograms/
│
├── template-parts/
│   ├── header-custom.php
│   ├── footer-custom.php
│   └── content-product.php
│
├── woocommerce/               # Templates WooCommerce personnalisés
│   ├── archive-product.php
│   ├── single-product.php
│   └── content-single-product.php
│
├── inc/
│   ├── woocommerce-setup.php
│   ├── custom-fields.php
│   └── b2b-functions.php
│
├── functions.php              # Fichier principal de fonctions
├── style.css                  # Métadonnées du thème (obligatoire)
├── index.php                  # Template principal
├── header.php
├── footer.php
├── front-page.php            # Page d'accueil
├── page.php                  # Template page
├── single.php                # Template article
├── screenshot.png            # Aperçu du thème (1200x900px)
│
├── package.json              # Dépendances npm
├── tailwind.config.js        # Configuration Tailwind
├── postcss.config.js         # Configuration PostCSS
└── README.md