import { ref, computed } from 'vue';

const themeColor = ref('#ef4444');

export function useTheme() {
  const setThemeColor = (color) => {
    themeColor.value = color;
  };

  const buttonClass = computed(() => {
    return `bg-[${themeColor.value}] hover:bg-[${themeColor.value}]/90`;
  });

  const buttonStyle = computed(() => {
    return {
      backgroundColor: themeColor.value,
    };
  });

  const buttonHoverStyle = (isHovered) => {
    if (isHovered) {
      return {
        backgroundColor: themeColor.value,
        opacity: 0.9,
      };
    }
    return {
      backgroundColor: themeColor.value,
    };
  };

  return {
    themeColor,
    setThemeColor,
    buttonClass,
    buttonStyle,
    buttonHoverStyle,
  };
}
