import { Stack } from 'expo-router';

export default function RegisterLayout() {
    return (
        <Stack screenOptions={{ headerShown: false }}>
            <Stack.Screen name="role" />
            <Stack.Screen name="details" />
            <Stack.Screen name="password" />
            <Stack.Screen name="success" />
        </Stack>
    );
}
