import React from 'react';
import { View } from 'react-native';
import Input from './Input';
import { spacing } from '../theme';

export default function SearchBar({ value, onChange, onSubmit }: { value: string; onChange: (v: string) => void; onSubmit: () => void }) {
  return (
    <View style={{ paddingHorizontal: spacing.lg, paddingTop: spacing.sm }}>
      <Input
        value={value}
        onChangeText={onChange}
        placeholder="Search by Job ID"
        keyboardType="number-pad"
        returnKeyType="search"
        onSubmitEditing={onSubmit}
      />
    </View>
  );
}
