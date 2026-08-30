/** @type {import('tailwindcss').Config} */
import { addDynamicIconSelectors } from "@iconify/tailwind";
import typography from "@tailwindcss/typography";
import daisyui from "daisyui";
import daisyuiThemes from "daisyui/src/theming/themes.js";

export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Livewire/**/*.php",
    "./app/Enums/**/*.php",
  ],
  theme: {
    container: {
      center: true,
      padding: "2rem",
    },
    extend: {
      fontFamily: {
        sans: ['Zain', 'sans-serif'],
      },
    },
  },
  plugins: [
    typography,
    daisyui,
    addDynamicIconSelectors(),
  ],
  daisyui: {
    themes: [
      {
        light: {
          ...daisyuiThemes["light"],
          error: "#ef5656",
          primary: "#00cc99",
          secondary: "#000033",
          accent: "#e9f3ff",
        },
      },
    ],
  },
};
