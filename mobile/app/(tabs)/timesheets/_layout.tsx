import { Stack } from 'expo-router';

export default function TimesheetsLayout() {
    return (
        <Stack screenOptions={{ headerShown: false }}>
            <Stack.Screen name="index" />
            <Stack.Screen name="[id]" />
            <Stack.Screen name="create/[jobId]" />
        </Stack>
    );
}
