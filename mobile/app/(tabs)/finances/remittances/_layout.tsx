import { Stack } from 'expo-router';

export default function RemittancesLayout() {
    return (
        <Stack screenOptions={{ headerShown: false }}>
            <Stack.Screen name="index" />
            <Stack.Screen name="create/[invoiceId]" />
        </Stack>
    );
}
