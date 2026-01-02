import { useState, useEffect } from 'react'
import { useParams } from 'react-router-dom'
import { theoryService } from '../../services/theoryService'
import ReactMarkdown from 'react-markdown'
import { BookOpenIcon, MagnifyingGlassIcon } from '@heroicons/react/24/outline'

const TheoryPage = () => {
    const { slug } = useParams()
    const [topics, setTopics] = useState([])
    const [selectedTopic, setSelectedTopic] = useState(null)
    const [theories, setTheories] = useState([])
    const [loading, setLoading] = useState(true)
    const [searchQuery, setSearchQuery] = useState('')

    useEffect(() => {
        loadTopics()
    }, [])

    useEffect(() => {
        if (slug && topics.length > 0) {
            const topic = topics.find(t => t.slug === slug)
            if (topic) {
                setSelectedTopic(topic)
                loadTopicTheories(topic.slug)
            }
        }
    }, [slug, topics])

    const loadTopics = async () => {
        try {
            const response = await theoryService.getTopics()
            if (response.success) {
                setTopics(response.data)
                if (!slug && response.data.length > 0) {
                    setSelectedTopic(response.data[0])
                    loadTopicTheories(response.data[0].slug)
                }
            }
        } catch (error) {
            console.error(error)
        } finally {
            setLoading(false)
        }
    }

    const loadTopicTheories = async (topicSlug) => {
        try {
            console.log('Loading theories for topic:', topicSlug)
            const response = await theoryService.getTopic(topicSlug)
            console.log('Topic response:', response)
            if (response.success) {
                const theoriesData = response.data.theories || response.data || []
                console.log('Theories data:', theoriesData)
                setTheories(theoriesData)
            }
        } catch (error) {
            console.error('Error loading theories:', error)
        }
    }

    const handleTopicClick = (topic) => {
        setSelectedTopic(topic)
        loadTopicTheories(topic.slug)
        setSearchQuery('')
    }

    const filteredTheories = theories.filter(theory =>
        theory.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        theory.content.toLowerCase().includes(searchQuery.toLowerCase())
    )

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-[60vh]">
                <div className="spinner w-16 h-16"></div>
            </div>
        )
    }

    return (
        <div className="animate-fade-in">
            {/* Header */}
            <div className="card mb-6">
                <div className="flex items-center justify-between mb-4">
                    <div className="flex items-center space-x-3">
                        <div className="p-3 bg-gradient-to-br from-primary-500 to-purple-600 rounded-xl">
                            <BookOpenIcon className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 className="text-3xl font-bold text-gradient">Lý Thuyết</h1>
                            <p className="text-gray-600">Tổng hợp kiến thức đầy đủ</p>
                        </div>
                    </div>
                </div>

                {/* Search */}
                <div className="relative">
                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <input
                        type="text"
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="input pl-10"
                        placeholder="Tìm kiếm lý thuyết..."
                    />
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {/* Sidebar - Topics */}
                <div className="lg:col-span-1">
                    <div className="card sticky top-24">
                        <h3 className="font-bold text-gray-900 mb-4">Chủ đề</h3>
                        <div className="space-y-2">
                            {topics.map((topic) => (
                                <button
                                    key={topic.id}
                                    onClick={() => handleTopicClick(topic)}
                                    className={`w-full text-left px-4 py-3 rounded-lg transition-all ${selectedTopic?.id === topic.id
                                        ? 'bg-primary-500 text-white shadow-lg'
                                        : 'bg-gray-50 text-gray-700 hover:bg-gray-100'
                                        }`}
                                >
                                    <div className="font-medium">{topic.name}</div>
                                    {topic.description && (
                                        <div className="text-xs mt-1 opacity-80">
                                            {topic.description}
                                        </div>
                                    )}
                                </button>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Main content - Theories */}
                <div className="lg:col-span-3 space-y-6">
                    {filteredTheories.length === 0 ? (
                        <div className="card text-center py-12">
                            <p className="text-gray-600">
                                {searchQuery ? 'Không tìm thấy kết quả' : 'Chưa có lý thuyết cho chủ đề này'}
                            </p>
                        </div>
                    ) : (
                        filteredTheories.map((theory) => (
                            <div key={theory.id} className="card">
                                <h2 className="text-2xl font-bold text-gray-900 mb-4 pb-4 border-b border-gray-200">
                                    {theory.title}
                                </h2>
                                <div className="prose prose-sm max-w-none">
                                    <ReactMarkdown
                                        components={{
                                            h1: ({ children }) => <h1 className="text-2xl font-bold text-gray-900 mt-6 mb-4">{children}</h1>,
                                            h2: ({ children }) => <h2 className="text-xl font-bold text-gray-900 mt-5 mb-3">{children}</h2>,
                                            h3: ({ children }) => <h3 className="text-lg font-bold text-gray-900 mt-4 mb-2">{children}</h3>,
                                            p: ({ children }) => <p className="text-gray-700 mb-3 leading-relaxed">{children}</p>,
                                            ul: ({ children }) => <ul className="list-disc list-inside space-y-1 mb-4 text-gray-700">{children}</ul>,
                                            ol: ({ children }) => <ol className="list-decimal list-inside space-y-1 mb-4 text-gray-700">{children}</ol>,
                                            code: ({ inline, children }) =>
                                                inline ? (
                                                    <code className="bg-gray-100 px-1.5 py-0.5 rounded text-sm text-primary-600 font-mono">{children}</code>
                                                ) : (
                                                    <code className="block bg-gray-900 text-gray-100 p-4 rounded-lg mb-4 overflow-x-auto text-sm font-mono">{children}</code>
                                                ),
                                            pre: ({ children }) => <pre className="mb-4">{children}</pre>,
                                            table: ({ children }) => (
                                                <div className="overflow-x-auto mb-4">
                                                    <table className="min-w-full border border-gray-300">{children}</table>
                                                </div>
                                            ),
                                            th: ({ children }) => <th className="border border-gray-300 px-4 py-2 bg-gray-50 font-bold text-left">{children}</th>,
                                            td: ({ children }) => <td className="border border-gray-300 px-4 py-2">{children}</td>,
                                        }}
                                    >
                                        {theory.content}
                                    </ReactMarkdown>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </div>
    )
}

export default TheoryPage
