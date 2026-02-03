import React from 'react';
import { View, Text, Pressable } from 'react-native';
import { colors, spacing, typography } from '../theme';
import { Ionicons } from '@expo/vector-icons';

export default function Header({ title, onAction }: { title: string; onAction?: () => void }) {
  return (
    <View style={{ paddingHorizontal: spacing.lg, paddingVertical: spacing.md, borderBottomWidth: 1, borderColor: colors.border, backgroundColor: colors.surface, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', borderRadius: 16, margin: spacing.lg, shadowColor: '#000000', shadowOpacity: 0.06, shadowRadius: 10, shadowOffset: { width: 0, height: 4 }, elevation: 2 }}>
      <Text style={typography.title}>{title}</Text>
      {onAction ? (
        <Pressable onPress={onAction} accessibilityRole="button" accessibilityLabel="Open filters">
          <Ionicons name="options-outline" size={22} color={colors.text} />
        </Pressable>
      ) : null}
    </View>
  );
}
