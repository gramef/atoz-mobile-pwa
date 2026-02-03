import React from 'react';
import { Pressable, Text } from 'react-native';
import { colors, spacing, radius } from '../theme';

export default function Chip({ label, onRemove }: { label: string; onRemove?: () => void }) {
  return (
    <Pressable onPress={onRemove} accessibilityRole="button" style={{ backgroundColor: colors.surface, paddingVertical: spacing.xs, paddingHorizontal: spacing.sm, borderRadius: radius.sm, marginRight: spacing.sm, borderWidth: 1, borderColor: colors.border }}>
      <Text style={{ color: colors.text }}>{label}</Text>
    </Pressable>
  );
}
