import { View, Text, TouchableOpacity, FlatList } from 'react-native';
import { useEffect, useState } from 'react';
import styles from '../../Create/styles';
import { MaterialIcons, Octicons, Ionicons } from '@expo/vector-icons';
import { darkGray, lightPurple, white } from '../../../color';

export default function SideBar({stateSideBar, handleSlideSideBar, deleteArea}) {
  const [data, setData] = useState([])
  const bgColor = {discord: "#5470d6", spotify: "#10a143",
                  twitch: "#9146ff", gmail: "#EA4335",
                  twitter: "#1da1f2", github: darkGray}

  const renderItem = ({item}) => {
    return (
      <>
        <View style={[styles.actionBlocks, styles.elevation, {backgroundColor: bgColor[item.service], width: 220}]}>
          <Text style={styles.textActionBlocks}>{item.name}</Text>
        </View>
        {
          item.number !== (stateSideBar[1].automation_actions.length - 1) ?
            <Octicons name="arrow-down" size={24} color="white" style={{alignSelf: 'center'}}/>
            : null
        }
      </>
    )
  }

  useEffect(() => {
    if (!stateSideBar[0])
      setData([])
    else
      setData(stateSideBar[1].automation_actions)
  }, [stateSideBar])

  return (
    <View style={[styles.sideBar, styles.elevation, stateSideBar[0] ? {width: '75%', backgroundColor: lightPurple} : null]}>
      <View style={{flex: 0.5, justifyContent: 'center', alignItems: 'center', paddingLeft: '10%'}}>
        <TouchableOpacity activeOpacity={0.6} onPressOut={() => handleSlideSideBar(null)} style={{position: 'absolute', left: 0, top: '47%'}}>
          <MaterialIcons name="keyboard-arrow-left" size={60} color={white}/>
        </TouchableOpacity>
        {stateSideBar[1] &&
          <FlatList
            data={data}
            renderItem={renderItem}
            keyExtractor={item => item.number}
            contentContainerStyle={{flex: 0.9, alignItems: 'center', justifyContent: 'center'}}
          />
        }
        <TouchableOpacity activeOpacity={0.6} onPressOut={() => deleteArea(stateSideBar[1].id)} style={stateSideBar[0] ? {position: 'absolute', bottom: 0, right: 25} : null}>
          <Ionicons name="md-trash-sharp" size={60} color={white}/>
        </TouchableOpacity>
      </View>
    </View>
  )
}