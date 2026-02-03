import { Stack } from 'expo-router';

export default function FinancesLayout() {
    return (
        <Stack screenOptions={{ headerShown: false }}>
            <Stack.Screen name="index" />
            <Stack.Screen name="agent-invoices" />
            <Stack.Screen name="client-invoices" />
            <Stack.Screen name="remittances" />
        </Stack>
    );
}
