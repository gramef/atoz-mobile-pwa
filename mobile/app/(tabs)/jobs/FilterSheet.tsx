import React, { useEffect, useState } from 'react';
import { Modal, View, Text, Pressable, ScrollView } from 'react-native';
import { colors, spacing, radius, typography } from '../../../src/ui/theme';
import { getLanguages, getStatuses } from '../../../src/api/client';
import Input from '../../../src/ui/components/Input';

type Props = {
  visible: boolean;
  onClose: () => void;
  value: Record<string, any>;
  onApply: (filters: Record<string, any>) => void;
};

export default function FilterSheet({ visible, onClose, value = {}, onApply }: Props) {
  const [languages, setLanguages] = useState<Record<string, string>>({});
  const [statuses, setStatuses] = useState<Record<string, string>>({});
  const [typeInterpreter, setTypeInterpreter] = useState(value?.typeInterpreter ?? true);
  const [typeTranslator, setTypeTranslator] = useState(value?.typeTranslator ?? true);
  const [languageId, setLanguageId] = useState<string | null>(value?.language_id ?? null);
  const [status, setStatus] = useState<string | null>(value?.status ?? null);
  const [search, setSearch] = useState<string>(value?.search ?? '');
  const [range, setRange] = useState<string>(value?.range ?? '');

  useEffect(() => {
    async function loadMeta() {
      try {
        console.log('FilterSheet: Loading metadata...');
        const [langs, stats] = await Promise.all([getLanguages(), getStatuses()]);
        setLanguages((langs as any).data || {});
        setStatuses((stats as any).data || {});
      } catch (e) {
        console.error('FilterSheet: Failed to load metadata', e);
      }
    }
    if (visible) loadMeta();
  }, [visible]);

  function fmt(d: Date) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${dd}`;
  }

  function apply() {
    let date: string | undefined = undefined;
    const today = new Date();
    if (range === 'today') {
      const s = fmt(today);
      date = s;
    } else if (range === 'next7') {
      const start = fmt(today);
      const end = fmt(new Date(today.getTime() + 6 * 24 * 60 * 60 * 1000));
      date = `${start} to ${end}`;
    } else if (range === 'last30') {
      const end = fmt(today);
      const start = fmt(new Date(today.getTime() - 29 * 24 * 60 * 60 * 1000));
      date = `${start} to ${end}`;
    }
    onApply({
      typeInterpreter,
      typeTranslator,
      language_id: languageId,
      status,
      search,
      date,
      range,
    });
    onClose();
  }

  return (
    <Modal visible={visible} animationType="slide" presentationStyle="pageSheet" onRequestClose={onClose}>
      <View style={{ flex: 1, backgroundColor: colors.bg }}>
        <View style={{ paddingHorizontal: spacing.lg, paddingVertical: spacing.md, borderBottomWidth: 1, borderColor: colors.border }}>
          <Text style={typography.title}>Filters</Text>
        </View>
        <ScrollView contentContainerStyle={{ padding: spacing.lg }}>
          <Text style={typography.h1}>Search</Text>
          <Input value={search} onChangeText={setSearch} placeholder="Job ID" keyboardType="number-pad" />

          <Text style={typography.h1}>Types</Text>
          <View style={{ flexDirection: 'row', gap: spacing.sm, marginBottom: spacing.lg }}>
            <Pressable onPress={() => setTypeInterpreter(!typeInterpreter)} style={{ padding: spacing.sm, borderWidth: 1, borderColor: colors.border, borderRadius: radius.sm, backgroundColor: typeInterpreter ? colors.card : colors.bg }}>
              <Text>Interpreter</Text>
            </Pressable>
            <Pressable onPress={() => setTypeTranslator(!typeTranslator)} style={{ padding: spacing.sm, borderWidth: 1, borderColor: colors.border, borderRadius: radius.sm, backgroundColor: typeTranslator ? colors.card : colors.bg }}>
              <Text>Translator</Text>
            </Pressable>
          </View>

          <Text style={typography.h1}>Language</Text>
          <View style={{ marginBottom: spacing.lg }}>
            {Object.entries(languages).slice(0, 50).map(([id, name]) => (
              <Pressable key={id} onPress={() => setLanguageId(id)} style={{ paddingVertical: spacing.sm }}>
                <Text style={{ color: languageId === id ? colors.primary : colors.text }}>{name}</Text>
              </Pressable>
            ))}
          </View>

          <Text style={typography.h1}>Status</Text>
          <View>
            {Object.entries(statuses).map(([id, name]) => (
              <Pressable key={id} onPress={() => setStatus(id)} style={{ paddingVertical: spacing.sm }}>
                <Text style={{ color: status === id ? colors.primary : colors.text }}>{name}</Text>
              </Pressable>
            ))}
          </View>

          <Text style={{ ...typography.h1, marginTop: spacing.lg }}>Date</Text>
          <View style={{ flexDirection: 'row', gap: spacing.sm }}>
            <Pressable onPress={() => setRange('today')} style={{ padding: spacing.sm, borderWidth: 1, borderColor: colors.border, borderRadius: radius.sm, backgroundColor: range === 'today' ? colors.card : colors.bg }}>
              <Text>Today</Text>
            </Pressable>
            <Pressable onPress={() => setRange('next7')} style={{ padding: spacing.sm, borderWidth: 1, borderColor: colors.border, borderRadius: radius.sm, backgroundColor: range === 'next7' ? colors.card : colors.bg }}>
              <Text>Next 7 days</Text>
            </Pressable>
            <Pressable onPress={() => setRange('last30')} style={{ padding: spacing.sm, borderWidth: 1, borderColor: colors.border, borderRadius: radius.sm, backgroundColor: range === 'last30' ? colors.card : colors.bg }}>
              <Text>Last 30 days</Text>
            </Pressable>
          </View>
        </ScrollView>

        <View style={{ padding: spacing.lg, borderTopWidth: 1, borderColor: colors.border }}>
          <Pressable onPress={apply} style={{ backgroundColor: colors.primary, padding: spacing.md, borderRadius: radius.md }}>
            <Text style={{ color: colors.primaryText, textAlign: 'center' }}>Apply</Text>
          </Pressable>
        </View>
      </View>
    </Modal>
  );
}
