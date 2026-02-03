import React from 'react';
import { View, DimensionValue } from 'react-native';
import { colors, spacing, radius } from '../theme';

export function SkeletonLine({ width = '100%', height = 16 }: { width?: DimensionValue; height?: number }) {
  return <View style={{ width, height, backgroundColor: '#E2E8F0', borderRadius: radius.md, marginBottom: spacing.sm, opacity: 0.5 }} />;
}

export function ListSkeleton({ count = 8 }: { count?: number }) {
  return (
    <View style={{ paddingHorizontal: spacing.lg }}>
      {Array.from({ length: count }).map((_, idx) => (
        <View key={idx} style={{ paddingVertical: spacing.md, marginVertical: spacing.sm, backgroundColor: colors.surface, padding: spacing.lg, borderRadius: 16 }}>
          <SkeletonLine width="60%" />
          <SkeletonLine width="40%" />
        </View>
      ))}
    </View>
  );
}
