import React from 'react';
import { View, Text, Pressable, Linking, Alert } from 'react-native';
import { colors, spacing, radius, typography } from '../theme';

export default function MapCard({ lat, lng, address }: { lat?: number | null; lng?: number | null; address?: { line_1?: string; line_2?: string; county?: string; postcode?: string } }) {
  const hasCoords = typeof lat === 'number' && typeof lng === 'number';
  const addrStr = [address?.line_1, address?.line_2, address?.county, address?.postcode].filter(Boolean).join(', ');
  const query = hasCoords ? `${lat},${lng}` : addrStr ? encodeURIComponent(addrStr) : '';
  const mapsUrl = query ? `https://www.google.com/maps/search/?api=1&query=${query}` : '';

  async function openMaps() {
    if (!mapsUrl) {
      Alert.alert('Location not available');
      return;
    }
    const can = await Linking.canOpenURL(mapsUrl);
    if (can) {
      Linking.openURL(mapsUrl);
    } else {
      Alert.alert('Unable to open Maps');
    }
  }

  return (
    <View style={{ borderWidth: 1, borderColor: colors.border, backgroundColor: colors.surface, borderRadius: radius.lg, padding: spacing.md, marginTop: spacing.md, shadowColor: '#000000', shadowOpacity: 0.06, shadowRadius: 10, shadowOffset: { width: 0, height: 4 }, elevation: 2 }}>
      <Text style={typography.h1}>Location</Text>
      {addrStr ? <Text style={typography.hint}>{addrStr}</Text> : null}
      <Pressable onPress={openMaps} disabled={!mapsUrl} accessibilityState={{ disabled: !mapsUrl }} style={{ marginTop: spacing.sm, backgroundColor: colors.primary, padding: spacing.sm, borderRadius: radius.md }}>
        <Text style={{ color: colors.primaryText, textAlign: 'center' }}>Open in Maps</Text>
      </Pressable>
    </View>
  );
}
