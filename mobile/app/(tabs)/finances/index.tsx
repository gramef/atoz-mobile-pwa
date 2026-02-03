import { Redirect } from 'expo-router';

// Redirect to agent invoices by default
export default function FinancesIndex() {
    return <Redirect href="/finances/agent-invoices" />;
}
