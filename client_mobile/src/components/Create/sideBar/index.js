import { View, Text, TouchableOpacity, TextInput } from 'react-native';
import styles from '../styles';
import { MaterialIcons } from '@expo/vector-icons';
import { black } from '../../../color';

export default function SideBar({stateSideBar, handleSlideSideBar}) {
  let key = 0
  console.log(stateSideBar[1])
  return (
    <View style={[styles.sideBar, styles.elevation, stateSideBar[0] ? {width: '75%'} : null]}>
      <View style={{flex: 0.5, justifyContent: 'center', alignItems: 'center', paddingLeft: '10%'}}>
        <TouchableOpacity activeOpacity={0.6} onPressOut={() => handleSlideSideBar(null)} style={{position: 'absolute', left: 0}}>
          <MaterialIcons name="keyboard-arrow-left" size={60} color={black}/>
        </TouchableOpacity>
        {stateSideBar[1] ?
          stateSideBar[1].fields.map(element => {
            let typeElement = null
            key++
            switch (element.type) {
              case "text":
                typeElement = <TextInput placeholder={element.name} multiline={true} style={styles.inputText}/>
                break;
              default:
                break;
            }
            return (
              <View key={key}>
                <Text>{element.title}</Text>
                {typeElement}
              </View>
            )
          })
          : null
        }
      </View>
    </View>
  )
}