import React from 'react';
import { View, Text, Pressable, Linking } from 'react-native';
import { colors, spacing, radius, typography } from '../theme';
import { getDocumentSignedUrl } from '../../api/client';

type Doc = { id: number; type?: string; name?: string };

export default function DocumentsList({ items }: { items: Doc[] }) {
  return (
    <View style={{ borderWidth: 1, borderColor: colors.border, backgroundColor: colors.surface, borderRadius: radius.lg, padding: spacing.md, marginTop: spacing.md, shadowColor: '#000000', shadowOpacity: 0.06, shadowRadius: 10, shadowOffset: { width: 0, height: 4 }, elevation: 2 }}>
      <Text style={typography.h1}>Documents</Text>
      {items && items.length > 0 ? (
        items.map((d) => (
          <Pressable
            key={d.id}
            onPress={async () => {
              try {
                const res = await getDocumentSignedUrl(d.id);
                const url = res.data?.url;
                if (url) Linking.openURL(url);
              } catch {}
            }}
            style={{ paddingVertical: spacing.sm }}
          >
            <Text style={{ color: colors.text }}>{d.name || `Document #${d.id}`}</Text>
          </Pressable>
        ))
      ) : (
        <Text style={typography.hint}>No documents</Text>
      )}
    </View>
  );
}
