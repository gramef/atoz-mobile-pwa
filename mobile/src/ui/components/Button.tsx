import React from 'react';
import { Pressable, Text } from 'react-native';
import { colors, spacing, radius, typography } from '../theme';

export default function Button({ title, onPress, variant = 'primary', disabled = false, style, textStyle }: { title: string; onPress: () => void; variant?: 'primary' | 'secondary' | 'outline'; disabled?: boolean; style?: any; textStyle?: any }) {
  let bg = variant === 'primary' ? colors.primary : colors.surface;
  let txt = variant === 'primary' ? colors.primaryText : colors.text;
  let borderWidth = 0;
  let borderColor = 'transparent';

  if (variant === 'outline') {
    bg = 'transparent';
    txt = colors.primary;
    borderWidth = 1;
    borderColor = colors.primary;
  }

  if (disabled) {
    bg = '#E2E8F0';
    txt = '#94A3B8';
    borderWidth = 0;
  }

  return (
    <Pressable
      onPress={disabled ? undefined : onPress}
      style={{
        backgroundColor: bg,
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.lg,
        borderRadius: radius.lg,
        shadowColor: '#000000',
        shadowOpacity: disabled ? 0 : 0.1,
        shadowRadius: 12,
        shadowOffset: { width: 0, height: 6 },
        elevation: disabled ? 0 : 3,
        borderWidth,
        borderColor,
        ...style,
      }}
    >
      <Text style={{ ...typography.h1, color: txt, textAlign: 'center', ...textStyle }}>{title}</Text>
    </Pressable>
  );
}
