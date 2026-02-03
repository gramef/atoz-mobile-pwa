import React from 'react';
import { View, Text, TextInput } from 'react-native';
import { useTheme, spacing, radius, typography } from '../theme';

type Props = React.ComponentProps<typeof TextInput> & {
  label?: string;
  labelActionText?: string;
  onLabelAction?: () => void;
  endAdornment?: React.ReactNode;
};

export default function Input(props: Props) {
  const { colors } = useTheme();
  const { label, labelActionText, onLabelAction, endAdornment, ...rest } = props;

  return (
    <View style={{ marginBottom: spacing.md }}>
      {label ? (
        <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: spacing.xs }}>
          <Text style={{ ...typography.hint, color: colors.text }}>{label}</Text>
          {labelActionText ? (
            <Text onPress={onLabelAction} style={{ ...typography.hint, color: colors.primary }}>{labelActionText}</Text>
          ) : null}
        </View>
      ) : null}
      <View>
        <TextInput
          {...rest}
          placeholderTextColor={(props as any).placeholderTextColor ?? colors.subtext}
          style={{
            borderWidth: 1,
            borderColor: colors.border,
            backgroundColor: colors.surface,
            borderRadius: radius.lg,
            paddingVertical: spacing.md,
            paddingHorizontal: spacing.lg,
            color: colors.text,
            shadowColor: '#000000',
            shadowOpacity: 0.03,
            shadowRadius: 6,
            shadowOffset: { width: 0, height: 2 },
            ...(props.style as any),
          }}
        />
        {endAdornment ? (
          <View style={{ position: 'absolute', right: spacing.md, top: '50%' as any, transform: [{ translateY: -12 }] }}>{endAdornment}</View>
        ) : null}
      </View>
    </View>
  );
}
