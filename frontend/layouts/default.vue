<template>
  <div class="min-h-screen flex flex-col">
    <svg aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden">
      <defs>
        <filter id="sticker-outline" color-interpolation-filters="sRGB">
          <feMorphology operator="dilate" radius="4" in="SourceAlpha" result="expanded" />
          <feFlood flood-color="white" flood-opacity="1" result="color" />
          <feComposite operator="in" in="color" in2="expanded" result="outline" />
          <feMerge>
            <feMergeNode in="outline" />
            <feMergeNode in="SourceGraphic" />
          </feMerge>
        </filter>
      </defs>
    </svg>
    <AppHeader />
    <main class="flex-1">
      <slot />
    </main>
    <AppFooter />
  </div>
</template>
