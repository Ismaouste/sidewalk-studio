import { onBeforeUnmount, onMounted } from 'vue';
import type { Ref } from 'vue';

export function useUnsavedChangesWarning(hasChanges: Ref<boolean>) {
    function beforeUnload(event: BeforeUnloadEvent) {
        if (!hasChanges.value) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    }

    onMounted(() => {
        window.addEventListener('beforeunload', beforeUnload);
    });

    onBeforeUnmount(() => {
        window.removeEventListener('beforeunload', beforeUnload);
    });
}
