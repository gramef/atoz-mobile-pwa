import React from 'react';
import { View, Text, Pressable } from 'react-native';
import { colors, spacing, typography } from '../theme';
import { Ionicons } from '@expo/vector-icons';

export default function ListItem({ title, subtitle, onPress, icon }: { title: string; subtitle?: string; onPress: () => void; icon?: any }) {
  return (
    <Pressable
      onPress={onPress}
      style={({ pressed }) => ({
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: spacing.lg,
        paddingVertical: spacing.md,
        backgroundColor: colors.surface,
        marginHorizontal: spacing.lg,
        marginVertical: spacing.sm,
        borderRadius: 16,
        shadowColor: '#000000',
        shadowOpacity: 0.06,
        shadowRadius: 10,
        shadowOffset: { width: 0, height: 4 },
        elevation: 2,
        opacity: pressed ? 0.9 : 1
      })}
    >
      <View style={{ width: 40, height: 40, borderRadius: 20, backgroundColor: colors.bg, alignItems: 'center', justifyContent: 'center', marginRight: spacing.md }}>
        <Ionicons name={icon || 'document-text-outline'} size={20} color={colors.primary} />
      </View>
      <View style={{ flex: 1 }}>
        <Text style={{ ...typography.h1, fontSize: 16 }}>{title}</Text>
        {subtitle ? <Text style={{ ...typography.hint, marginTop: 2 }}>{subtitle}</Text> : null}
      </View>
      <Ionicons name="chevron-forward" size={16} color={colors.subtext} />
    </Pressable>
  );
}
