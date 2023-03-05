import { View, Text, FlatList, TouchableOpacity, Image, Alert } from 'react-native';
import { darkPurple, lightPurple, white } from '../color';
import { MaterialIcons, Fontisto, FontAwesome5, Feather } from '@expo/vector-icons';
import { useCallback, useEffect, useState } from 'react';
import { useGetAutomation, useDeleteAutomation } from '../api/apiMyAreasPage';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import styles from '../components/MyAreas/styles';
import styles2 from '../components/Create/styles'
import SideBar from '../components/MyAreas/sideBar';
import MenuBar from '../components/MyAreas/menuBar';

export default function MyAreas({navigation}) {
  const [data, setData] = useState([])
  const [stateSideBar, setStateSideBar] = useState([false, null])
  const [stateMenuBar, setStateMenuBar] = useState(false)
  const getAutomation = useGetAutomation()
  const deleteAutomation = useDeleteAutomation()

  useEffect(() => {
    getAutomation.mutate(null, {
      onSuccess: (data) => {
        setData(data.data.automations)
      }
    })
  }, [])

  useEffect(() => {
    const unsubscribe = navigation.addListener('tabPress', () => {
      if (!navigation.isFocused()) {
        getAutomation.mutate(null, {
          onSuccess: (data) => {
            setData(data.data.automations)
          }
        })
      }
    })
    return unsubscribe
  }, [navigation])

  const handleSlideSideBar = useCallback(function(item) {
    setStateSideBar(s => [!s[0], item])
  }, [])

  const handleSlideMenuBar = useCallback(function() {
    setStateMenuBar(m => !m)
  }, [])

  const deleteArea = useCallback(function(automation_id) {
    deleteAutomation.mutate(JSON.stringify({automation_id: automation_id}), {
      onSuccess: () => {
        Alert.alert("Success", "Your Area was successfully deleted!")
        getAutomation.mutate(null, {
          onSuccess: (data) => {
            setData(data.data.automations)
          }
        })
        handleSlideSideBar(null)
      }
    })
  }, [])

  const handleLogOut = useCallback(function() {
    AsyncStorage.removeItem("token")
    .then((res) => {
      navigation.replace('SignIn')
    })
  }, [])

  const handleChangeServerIp = useCallback(function(event) {
    if (event.nativeEvent.text !== null && event.nativeEvent.text !== "") {
      axios.defaults.baseURL = event.nativeEvent.text
      AsyncStorage.setItem("serverIp", axios.defaults.baseURL)
      .then((res) => {
        Alert.alert("Success", "Server IP Address successfully updated!")
        getAutomation.mutate(null, {
          onSuccess: (data) => {
            setData(data.data.automations)
          }
        })
      })
    } else
      Alert.alert("Missing Server IP", "Please enter the server IP Address")
  }, [])

  const renderItem = ({item, index}) => {
    let key = 0
    let iconArray = []
    return (
      <View style={[styles2.actionBlocks, styles2.elevation, {backgroundColor: lightPurple, marginTop: 4, marginBottom: 4}]}>
        <View style={styles.containerIconServices}>
          {
            item.automation_actions.map(element => {
              let iconElement = null
              key++
              if (iconArray.find(elem => elem === element.service) !== undefined)
                return null
              iconArray.push(element.service)
              switch (element.service) {
                case "spotify":
                  iconElement = <Fontisto name="spotify" size={35} color="#17d860"/>
                  break;
                case "discord":
                  iconElement = <Fontisto name="discord" size={35} color="#5765f2"/>
                  break;
                case "twitch":
                  iconElement = <FontAwesome5 name="twitch" size={33} color="#9146ff" />
                  break;
                case "twitter":
                  iconElement = <Fontisto name="twitter" size={30} color="#179cf0"/>
                  break;
                case "gmail":
                  iconElement = <Image
                  source={require('../../assets/images/gmailIcon.png')}
                  fadeDuration={0}
                  style={{width: 46, height: 27}}
                />
                  break;
                case "github":
                  iconElement = <Fontisto name="github" size={35} color="black"/>
                  break;
                default:
                  break;
              }
              return (
                <View key={key} style={{marginHorizontal: 2}}>
                  {iconElement}
                </View>
              )
            })
          }
        </View>
        <Text style={styles2.textActionBlocks}>{item.name}</Text>
        <TouchableOpacity activeOpacity={0.6} onPressOut={() => handleSlideSideBar(data[index])} style={styles2.arrowActionBlocks}>
          <MaterialIcons name="keyboard-arrow-right" size={48} color={white}/>
        </TouchableOpacity>
      </View>
    )
  }

  return (
    <View style={styles.container}>
      <TouchableOpacity activeOpacity={0.6} onPressOut={handleSlideMenuBar} style={{position: 'absolute', left: 5, top: '8.5%'}}>
        <Feather name="menu" size={48} color={darkPurple}/>
      </TouchableOpacity>
      <Text style={{fontSize: 34, fontWeight: 'bold', color: lightPurple}}>My AREAS</Text>
      <View style={styles.containerAreas}>
      {getAutomation.isSuccess ?
        <FlatList
          data={data}
          renderItem={renderItem}
          keyExtractor={item => item.id}
          contentContainerStyle={{alignItems: 'center'}}
          style={{marginVertical: 4, marginHorizontal: 18}}
        />
        : <Text style={{fontSize: 24, fontWeight: 'bold', alignSelf: 'center'}}>Loading...</Text>
      }
      </View>
      <SideBar stateSideBar={stateSideBar} handleSlideSideBar={handleSlideSideBar} deleteArea={deleteArea}/>
      <MenuBar stateMenuBar={stateMenuBar} handleSlideMenuBar={handleSlideMenuBar}
        handleLogOut={handleLogOut} handleChangeServerIp={handleChangeServerIp}
      />
    </View>
  )
}