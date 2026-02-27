import { ref } from 'vue';

// Shared state for module events
const moduleUpdateCounter = ref(0);

export function useModuleEvents() {
    const triggerModuleUpdate = () => {
        moduleUpdateCounter.value++;
    };

    return {
        moduleUpdateCounter,
        triggerModuleUpdate
    };
}
