/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './assets/**/*.js',
    './template-parts/**/*.php',
    './woocommerce/**/*.php',
    // Important : inclure tous les fichiers où vous utilisez Tailwind
  ],
  theme: {
    extend: {
      // Palette de couleurs Celya
      colors: {
        celya: {
          primary: '#59332A',      // Marron artisanal
          secondary: '#F2D0A7',    // Beige biscuit
          light: '#FAF9F8',        // Blanc
          dark: '#2C2C2C',         // Texte principal
          orange_dark: '#F2B28D',
          orange_light: '#FDECE2',
          blue_dark: '#BDD9F2',
          blue_light: '#F2F7FC',
          green_dark: '#ABE0A4',
          green_light: '#E9F6E8',
          yellow_dark: '#F2D479',
          yellow_light: '#FCF5DD',
          pink_dark: '#EDA2C1',
          pink_light: '#F9E8EE',
          red_dark: '#F25D5F',
          red_light: '#FA807F',
          grey_dark: '#2C2C2C',
          grey_light: '#F6F6F6',
        },
      },
      
      // ===================
      // TYPOGRAPHIE
      // ===================
      fontFamily: {
        // Montserrat pour le corps de texte (sans-serif)
          sans: [
            'Montserrat', 
            'system-ui',
            '-apple-system',
            'BlinkMacSystemFont',
            'Segoe UI',
            'Roboto',
            'Helvetica Neue',
            'Arial',
            'sans-serif'
          ],
          serif: [
            'Arima', 
            'Georgia',
            'Cambria',
            'Times New Roman',
            'Times',
            'serif'
          ],
      },
      
      // Font weights (déjà supportés par Montserrat et Arima)
      fontWeight: {
        light: '300',
        normal: '400',
        medium: '500',
        semibold: '600',
        bold: '700',
        extrabold: '800',
      },
      
      // ===================
      // ESPACEMENTS
      // ===================
      spacing: {
        '72': '18rem',
        '84': '21rem',
        '96': '24rem',
        '128': '32rem',
      },
      
      // ===================
      // BORDURES ARRONDIES
      // ===================
      borderRadius: {
        'celya': '4rem',
        'celya-s': '1rem',
        'celya-m': '1.5rem',
        'celya-l': '2rem',
        'celya-xl': '3rem',
      },
      
      // ===================
      // OMBRES
      // ===================
      boxShadow: {
        'celya': '0 8px 24px rgba(0, 0, 0, 0.1)',
        'celya-hover': '0 12px 32px rgba(0, 0, 0, 0.15)',
        'celya-sm': '0 4px 12px rgba(0, 0, 0, 0.08)',
        'celya-lg': '0 16px 48px rgba(0, 0, 0, 0.12)',
      },
      
      // ===================
      // TRANSITIONS
      // ===================
      transitionDuration: {
        '400': '400ms',
      },
      
      // ===================
      // ANIMATIONS
      // ===================
      animation: {
        'fade-in': 'fadeIn 0.6s ease-out',
        'slide-in-left': 'slideInLeft 0.6s ease-out',
        'slide-in-right': 'slideInRight 0.6s ease-out',
      },
      
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideInLeft: {
          '0%': { opacity: '0', transform: 'translateX(-30px)' },
          '100%': { opacity: '1', transform: 'translateX(0)' },
        },
        slideInRight: {
          '0%': { opacity: '0', transform: 'translateX(30px)' },
          '100%': { opacity: '1', transform: 'translateX(0)' },
        },
      },
      
      // ===================
      // LARGEURS MAX
      // ===================
      maxWidth: {
        '8xl': '88rem',
        '9xl': '96rem',
      },
      
      // ===================
      // LINE HEIGHT
      // ===================
      lineHeight: {
        'extra-relaxed': '1.75',
        'ultra-relaxed': '2',
      },
      
      // ===================
      // LETTER SPACING
      // ===================
      letterSpacing: {
        'ultra-wide': '0.15em',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),         // Styles pour formulaires
    require('@tailwindcss/typography'),    // Prose styling
    require('@tailwindcss/aspect-ratio'),  // Aspect ratios
  ],
}