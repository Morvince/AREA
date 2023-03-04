import { View, Text, FlatList, TouchableOpacity } from 'react-native';
import { lightPurple, white } from '../color';
import { MaterialIcons } from '@expo/vector-icons';
import styles from '../components/MyAreas/styles';
import styles2 from '../components/Create/styles'

const data = [{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},
{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},
{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},
{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},
{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},
{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'},{ok: 'li'}]
export default function MyAreas({navigation}) {
  const renderItem = ({item}) => {
    return (
      <View style={[styles2.actionBlocks, styles2.elevation, {backgroundColor: lightPurple, marginTop: 4, marginBottom: 4}]}>
        <Text style={styles2.textActionBlocks}>My AREAS</Text>
        <TouchableOpacity activeOpacity={0.6}>
          <MaterialIcons name="keyboard-arrow-down" size={48} color={white}/>
        </TouchableOpacity>
      </View>
    )
  }
  return (
    <View style={styles.container}>
      <Text style={{fontSize: 34, fontWeight: 'bold', color: lightPurple}}>My AREAS</Text>
      <View style={styles.containerAreas}>
        <FlatList
          data={data}
          renderItem={renderItem}
          keyExtractor={item => item.li}
          contentContainerStyle={{alignItems: 'center'}}
          style={{marginVertical: 4, marginHorizontal: 18}}
        />
      </View>
    </View>
  )
}