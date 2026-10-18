@tailwind base;
@tailwind components;
@tailwind utilities;

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Reusable thin scrollbar utility */
@layer utilities {
  .scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #d1d5db transparent;
  }
  .scrollbar::-webkit-scrollbar {
    width: 4px;
  }
  .scrollbar::-webkit-scrollbar-track {
    background: transparent;
  }
  .scrollbar::-webkit-scrollbar-thumb {
    background-color: #d1d5db;
    border-radius: 9999px;
  }
  .scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #9ca3af;
  }
}

@media (prefers-color-scheme: dark) {
  .scrollbar {
    scrollbar-color: #525252 transparent;
  }
  .scrollbar::-webkit-scrollbar-thumb {
    background-color: #525252;
  }
  .scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #737373;
  }
}

.dark .scrollbar {
  scrollbar-color: #525252 transparent;
}
.dark .scrollbar::-webkit-scrollbar-thumb {
  background-color: #525252;
}
.dark .scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #737373;
}